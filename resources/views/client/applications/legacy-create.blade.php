

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6"
     x-data="{
         step: 1,
         totalSteps: 3,
         saving: false,
         lastSaved: null,
         saveTimeout: null,
         dirty: false,
         get lastSavedLabel() {
             if (!this.lastSaved) return '';
             const diff = Math.floor((Date.now() - this.lastSaved) / 60000);
             return diff < 1 ? 'baru saja' : diff + ' mnt lalu';
         },
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
         advanceStep() {
             const form = document.getElementById('applicationForm');
             const visibleFields = form.querySelectorAll('[x-show] input[required], [x-show] select[required], [x-show] textarea[required]');
             const invalid = form.querySelectorAll(':invalid');
             if (invalid.length) {
                 const first = invalid[0];
                 first.scrollIntoView({ behavior: 'smooth', block: 'center' });
                 first.focus();
                 form.reportValidity();
                 return;
             }
             this.step++;
             this.$nextTick(() => {
                 const el = document.querySelector('[x-show]');
                 const firstInput = document.querySelector('[data-step="' + this.step + '"] input, [data-step="' + this.step + '"] select');
                 if (firstInput) firstInput.focus();
             });
         },
         async saveDraft() {
             this.saving = true;
             try {
                 const form = document.getElementById('applicationForm');
                 const data = new FormData(form);
                 data.append('save_as_draft', '1');
                 await window.apiFetch('{{ route('client.applications.store') }}', { method: 'POST', body: data });
                 this.lastSaved = Date.now();
                 this.dirty = false;
             } catch(e) { /* silent fail */ }
             this.saving = false;
         }
     }"
     @input="triggerAutoSave()">

    <!-- Mobile: Back Button Only -->
    <div class="sm:hidden mb-4">
        <a href="{{ route('client.services.show', $permitType->code) }}" 
           class="inline-flex items-center text-[#0a66c2] hover:text-[#004182] font-medium text-sm">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali
        </a>
    </div>

    <!-- Desktop: Breadcrumb -->
    <nav class="hidden sm:block mb-6 text-sm">
        <ol class="flex items-center space-x-2 text-gray-600 dark:text-gray-400">
            <li><a href="{{ route('client.services.index') }}" class="hover:text-[#0a66c2]">Katalog Layanan</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li><a href="{{ route('client.services.show', $permitType->code) }}" class="hover:text-[#0a66c2] max-w-sm truncate inline-block">{{ $permitType->name }}</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-900 dark:text-white font-medium">Ajukan Permohonan</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-4 sm:p-6 mb-6">
        <div class="flex flex-col sm:flex-row items-start gap-4">
            <div class="flex-shrink-0 w-12 h-12 bg-[#0a66c2]/10 dark:bg-[#0a66c2]/20 rounded-xl flex items-center justify-center">
                <i class="fas fa-file-alt text-[#0a66c2] text-xl"></i>
            </div>
            <div class="flex-1 min-w-0">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ $permitType->name }}</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $permitType->description }}</p>
                <div class="mt-3 flex flex-col sm:flex-row sm:flex-wrap gap-2 sm:gap-4 text-xs sm:text-sm">
                    <span class="text-gray-600 dark:text-gray-400 flex items-center">
                        <i class="fas fa-money-bill-wave mr-1 flex-shrink-0"></i>
                        <span class="truncate">Estimasi: Rp {{ number_format($permitType->estimated_cost_min, 0, ',', '.') }} - Rp {{ number_format($permitType->estimated_cost_max, 0, ',', '.') }}</span>
                    </span>
                    <span class="text-gray-600 dark:text-gray-400 flex items-center">
                        <i class="fas fa-clock mr-1 flex-shrink-0"></i>
                        Proses: {{ $permitType->avg_processing_days }} hari kerja
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Step Progress Bar -->
    <div class="mb-6">
        <!-- Mobile: Step X dari Y -->
        <div class="sm:hidden flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Langkah <span x-text="step"></span> dari <span x-text="totalSteps"></span></span>
            <span x-show="saving" class="text-xs text-[#0a66c2] flex items-center gap-1">
                <i class="fas fa-circle-notch fa-spin text-[10px]"></i> Menyimpan...
            </span>
            <span x-show="!saving && lastSaved" class="text-xs text-gray-400 flex items-center gap-1">
                <i class="fas fa-check text-green-500 text-[10px]"></i>
                <span x-text="'Tersimpan ' + lastSavedLabel"></span>
            </span>
        </div>
        <div class="relative">
            <!-- Track -->
            <div class="hidden sm:flex items-start justify-between relative">
                <div class="absolute top-4 left-0 right-0 h-0.5 bg-gray-200 dark:bg-gray-700 -z-0"></div>
                @foreach([
                    ['num' => 1, 'label' => 'Informasi Perusahaan', 'icon' => 'fa-building'],
                    ['num' => 2, 'label' => 'Penanggung Jawab',     'icon' => 'fa-user'],
                    ['num' => 3, 'label' => 'Konfirmasi & Kirim',   'icon' => 'fa-paper-plane'],
                ] as $s)
                <div class="flex flex-col items-center relative z-10 flex-1"
                     :class="{ 'opacity-40': {{ $s['num'] }} > step }">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-colors border-2"
                         :class="step >= {{ $s['num'] }}
                             ? 'bg-[#0a66c2] border-[#0a66c2] text-white'
                             : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-400'">
                        <template x-if="step > {{ $s['num'] }}">
                            <i class="fas fa-check text-[10px]"></i>
                        </template>
                        <template x-if="step <= {{ $s['num'] }}">
                            <span>{{ $s['num'] }}</span>
                        </template>
                    </div>
                    <span class="mt-1.5 text-xs font-medium text-center leading-tight max-w-[80px]"
                          :class="step >= {{ $s['num'] }} ? 'text-[#0a66c2] dark:text-blue-400' : 'text-gray-400'">{{ $s['label'] }}</span>
                </div>
                @endforeach
            </div>
            <!-- Mobile progress bar -->
            <div class="sm:hidden h-1.5 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                <div class="h-full bg-[#0a66c2] rounded-full progress-bar-fill"
                     :style="'width: ' + ((step / totalSteps) * 100) + '%'"></div>
            </div>
        </div>
        <!-- Auto-save indicator desktop -->
        <div class="hidden sm:flex items-center gap-2 mt-2 justify-end">
            <span x-show="saving" class="text-xs text-[#0a66c2] flex items-center gap-1">
                <i class="fas fa-circle-notch fa-spin text-[10px]"></i> Menyimpan draft...
            </span>
            <span x-show="!saving && lastSaved" class="text-xs text-gray-400 flex items-center gap-1">
                <i class="fas fa-check text-green-500 text-[10px]"></i>
                <span x-text="'Draft tersimpan ' + lastSavedLabel"></span>
            </span>
        </div>
    </div>

    <!-- Consultation Info Box -->
    <div class="bg-[#0a66c2]/5 dark:bg-[#0a66c2]/10 border-l-4 border-[#0a66c2] rounded-r-xl p-4 mb-6">
        <div class="flex flex-col sm:flex-row items-start gap-3">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-[#0a66c2]/10 dark:bg-[#0a66c2]/30 rounded-full flex items-center justify-center">
                    <i class="fas fa-comments text-[#0a66c2]"></i>
                </div>
            </div>
            <div class="flex-1 min-w-0">
                <h4 class="font-bold text-gray-900 dark:text-white mb-1 text-sm">
                    Butuh Konsultasi Sebelum Mengajukan?
                </h4>
                <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                    Tulis pertanyaan Anda di bagian <strong>"Catatan Tambahan"</strong> di bawah. 
                    Simpan sebagai draft, dan tim konsultan kami akan merespons dalam <strong>1x24 jam</strong> 
                    via sistem komunikasi yang tersedia di halaman detail aplikasi.
                </p>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('client.applications.store') }}" id="applicationForm">
        @csrf
        <input type="hidden" name="permit_type_id" value="{{ $permitType->id }}">

        <!-- Step 1: Company Information -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-4 sm:p-6 mb-6" x-show="step === 1" x-transition>
            <h2 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class="fas fa-building text-[#0a66c2] mr-2 flex-shrink-0"></i>
                <span>Informasi Perusahaan</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Company Name -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nama Perusahaan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="form_data[company_name]" 
                           value="{{ old('form_data.company_name', $draft->form_data['company_name'] ?? auth('client')->user()->company_name) }}"
                           required
                           autocomplete="organization"
                           class="w-full px-4 py-2 text-base border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-[#0a66c2] dark:bg-gray-700 dark:text-white">
                    @error('form_data.company_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Company Address -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Alamat Perusahaan <span class="text-red-500">*</span>
                    </label>
                    <textarea name="form_data[company_address]" 
                              rows="3"
                              required
                              autocomplete="street-address"
                              class="w-full px-4 py-2 text-base border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-[#0a66c2] dark:bg-gray-700 dark:text-white">{{ old('form_data.company_address', $draft->form_data['company_address'] ?? auth('client')->user()->address) }}</textarea>
                    @error('form_data.company_address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- NPWP -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        NPWP Perusahaan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="form_data[company_npwp]" 
                           value="{{ old('form_data.company_npwp', $draft->form_data['company_npwp'] ?? '') }}"
                           placeholder="00.000.000.0-000.000"
                           required
                           inputmode="numeric"
                           autocomplete="off"
                           class="w-full px-4 py-2 text-base border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-[#0a66c2] dark:bg-gray-700 dark:text-white">
                    @error('form_data.company_npwp')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nomor Telepon <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" 
                           name="form_data[company_phone]" 
                           value="{{ old('form_data.company_phone', $draft->form_data['company_phone'] ?? auth('client')->user()->phone) }}"
                           required
                           inputmode="tel"
                           autocomplete="tel"
                           pattern="[0-9+\s\-\(\)]+"
                           class="w-full px-4 py-2 text-base border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-[#0a66c2] dark:bg-gray-700 dark:text-white">
                    @error('form_data.company_phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- KBLI Code with Autocomplete -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Kode KBLI (Klasifikasi Baku Lapangan Usaha Indonesia)
                        <span class="text-xs text-gray-500 font-normal ml-1">- Opsional</span>
                    </label>
                    <div class="relative">
                        <input type="text" 
                               id="kbli_search"
                               placeholder="Ketik untuk mencari KBLI (min. 2 karakter)..."
                               autocomplete="off"
                               class="w-full px-4 py-2 text-base border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-[#0a66c2] dark:bg-gray-700 dark:text-white">
                        
                        <!-- Hidden inputs for actual data -->
                        <input type="hidden" name="kbli_code" id="kbli_code" value="{{ old('kbli_code', $draft->kbli_code ?? '') }}">
                        <input type="hidden" name="kbli_description" id="kbli_description" value="{{ old('kbli_description', $draft->kbli_description ?? '') }}">
                        
                        <!-- Autocomplete dropdown -->
                        <div id="kbli_dropdown" class="hidden absolute z-10 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl shadow-lg max-h-60 overflow-y-auto">
                            <!-- Results will be populated here -->
                        </div>

                        <!-- Loading indicator -->
                        <div id="kbli_loading" class="hidden absolute right-3 top-3">
                            <i class="fas fa-spinner fa-spin text-[#0a66c2]"></i>
                        </div>
                    </div>

                    <!-- Selected KBLI display -->
                    <div id="kbli_selected" class="hidden mt-2 p-2.5 sm:p-3 bg-[#0a66c2]/5 dark:bg-[#0a66c2]/20 border border-[#0a66c2]/20 dark:border-[#0a66c2]/30 rounded-xl">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-xs sm:text-sm font-bold text-[#0a66c2] dark:text-[#0a66c2]" id="selected_code"></span>
                                </div>
                                <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-300" id="selected_description"></p>
                            </div>
                            <button type="button" onclick="clearKBLI()" class="ml-2 text-red-600 hover:text-red-800 flex-shrink-0 w-6 h-6 flex items-center justify-center">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <p class="mt-1 text-xs text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        KBLI membantu kami memahami bidang usaha Anda sesuai dengan standar OSS (Online Single Submission)
                    </p>
                    @error('kbli_code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Step 2: PIC Information -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-4 sm:p-6 mb-6" x-show="step === 2" x-transition>
            <h2 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class="fas fa-user text-[#0a66c2] mr-2 flex-shrink-0"></i>
                <span>Penanggung Jawab (PIC)</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- PIC Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="form_data[pic_name]" 
                           value="{{ old('form_data.pic_name', $draft->form_data['pic_name'] ?? auth('client')->user()->name) }}"
                           required
                           autocomplete="name"
                           class="w-full px-4 py-2 text-base border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-[#0a66c2] dark:bg-gray-700 dark:text-white">
                    @error('form_data.pic_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- PIC Position -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Jabatan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="form_data[pic_position]" 
                           value="{{ old('form_data.pic_position', $draft->form_data['pic_position'] ?? '') }}"
                           required
                           autocomplete="organization-title"
                           class="w-full px-4 py-2 text-base border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-[#0a66c2] dark:bg-gray-700 dark:text-white">
                    @error('form_data.pic_position')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- PIC Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" 
                           name="form_data[pic_email]" 
                           value="{{ old('form_data.pic_email', $draft->form_data['pic_email'] ?? auth('client')->user()->email) }}"
                           required
                           inputmode="email"
                           autocomplete="email"
                           class="w-full px-4 py-2 text-base border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-[#0a66c2] dark:bg-gray-700 dark:text-white">
                    @error('form_data.pic_email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- PIC Phone -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nomor HP <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" 
                           name="form_data[pic_phone]" 
                           value="{{ old('form_data.pic_phone', $draft->form_data['pic_phone'] ?? auth('client')->user()->phone) }}"
                           required
                           inputmode="tel"
                           autocomplete="tel"
                           class="w-full px-4 py-2 text-base border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-[#0a66c2] dark:bg-gray-700 dark:text-white">
                    @error('form_data.pic_phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        </div>

        <!-- Step 3: Notes + Konfirmasi -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-4 sm:p-6 mb-6" x-show="step === 3" x-transition>
            {{-- Review summary --}}
            <div class="mb-5 p-4 bg-gray-50 dark:bg-gray-700/40 rounded-xl border border-gray-200 dark:border-gray-700">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Ringkasan Permohonan</p>
                <dl class="space-y-1.5 text-sm">
                    <div class="flex gap-2">
                        <dt class="w-28 text-gray-500 dark:text-gray-400 flex-shrink-0">Jenis Izin</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $permitType->name }}</dd>
                    </div>
                    <div class="flex gap-2">
                        <dt class="w-28 text-gray-500 dark:text-gray-400 flex-shrink-0">Estimasi Biaya</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">Rp {{ number_format($permitType->estimated_cost_min, 0, ',', '.') }} – Rp {{ number_format($permitType->estimated_cost_max, 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex gap-2">
                        <dt class="w-28 text-gray-500 dark:text-gray-400 flex-shrink-0">Estimasi Waktu</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $permitType->avg_processing_days }} hari kerja</dd>
                    </div>
                </dl>
            </div>

            <h2 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white mb-3 flex items-center">
                <i class="fas fa-sticky-note text-[#0a66c2] mr-2 flex-shrink-0"></i>
                <span>Catatan Tambahan (Opsional)</span>
            </h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Pertanyaan atau informasi khusus? Tim konsultan kami akan merespons dalam 1×24 jam.</p>
            <textarea name="form_data[notes]" 
                      rows="4"
                      placeholder="Contoh: Saya ingin konsultasi terlebih dahulu sebelum melanjutkan..."
                      class="w-full px-4 py-2.5 text-base border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-[#0a66c2] focus:border-[#0a66c2] dark:bg-gray-700 dark:text-white transition-colors">{{ old('form_data.notes', $draft->form_data['notes'] ?? '') }}</textarea>
        </div>

        <!-- Step Navigation / Form Actions -->
        <div class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 px-4 sm:px-6 py-4 sticky bottom-0 sm:static rounded-2xl sm:rounded-2xl shadow-md sm:shadow-sm">
            <div class="flex gap-3">
                {{-- Back --}}
                <template x-if="step === 1">
                    <a href="{{ route('client.services.show', $permitType->code) }}"
                       class="flex-1 sm:flex-none text-center px-5 py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-xl transition-colors font-medium text-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Batal
                    </a>
                </template>
                <template x-if="step > 1">
                    <button type="button" @click="step--"
                            class="flex-1 sm:flex-none px-5 py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-xl transition-colors font-medium text-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </button>
                </template>

                <div class="flex-1 flex gap-2 justify-end">
                    {{-- Save draft --}}
                    <button type="button" @click="saveDraft()"
                            :disabled="saving"
                            class="hidden sm:inline-flex items-center gap-2 px-5 py-3 bg-gray-600 hover:bg-gray-700 disabled:opacity-60 text-white rounded-xl transition-colors font-medium text-sm">
                        <i class="fas" :class="saving ? 'fa-circle-notch fa-spin' : 'fa-save'"></i>
                        <span>Draft</span>
                    </button>

                    {{-- Next step --}}
                    <template x-if="step < totalSteps">
                        <button type="button" @click="advanceStep()"
                                class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-6 py-3 bg-[#0a66c2] hover:bg-[#004182] text-white rounded-xl transition-colors font-semibold text-sm">
                            <span>Lanjutkan</span>
                            <i class="fas fa-arrow-right text-xs"></i>
                        </button>
                    </template>

                    {{-- Final submit --}}
                    <template x-if="step === totalSteps">
                        <div class="flex gap-2">
                            <button type="submit" name="save_as_draft" value="1"
                                    class="sm:hidden px-4 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-xl transition-colors font-medium text-sm">
                                <i class="fas fa-save"></i>
                            </button>
                            <button type="submit"
                                    class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-6 py-3 bg-[#0a66c2] hover:bg-[#004182] text-white rounded-xl transition-colors font-semibold text-sm">
                                <i class="fas fa-paper-plane text-xs"></i>
                                <span class="hidden sm:inline">Kirim Permohonan</span>
                                <span class="sm:hidden">Kirim</span>
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// KBLI Autocomplete
let kbliSearchTimeout = null;
let selectedKBLI = null;

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const kbliCode = document.getElementById('kbli_code').value;
    if (kbliCode) {
        // Load existing KBLI data
        showSelectedKBLI({
            code: document.getElementById('kbli_code').value,
            description: document.getElementById('kbli_description').value
        });
    }
});

document.getElementById('kbli_search').addEventListener('input', function(e) {
    const keyword = e.target.value.trim();
    
    // Clear previous timeout
    if (kbliSearchTimeout) {
        clearTimeout(kbliSearchTimeout);
    }
    
    // Hide dropdown if less than 2 characters
    if (keyword.length < 2) {
        document.getElementById('kbli_dropdown').classList.add('hidden');
        return;
    }
    
    // Show loading
    document.getElementById('kbli_loading').classList.remove('hidden');
    
    // Debounce search
    kbliSearchTimeout = setTimeout(() => {
        searchKBLI(keyword);
    }, 300);
});

function searchKBLI(keyword) {
    fetch(`/api/kbli/search?q=${encodeURIComponent(keyword)}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('kbli_loading').classList.add('hidden');
            
            if (data.success && data.data.length > 0) {
                displayKBLIResults(data.data);
            } else {
                displayNoResults();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('kbli_loading').classList.add('hidden');
            displayNoResults();
        });
}

function displayKBLIResults(results) {
    const dropdown = document.getElementById('kbli_dropdown');
    let html = '<div class="py-1 sm:py-2">';
    
    results.forEach(item => {
        html += `
            <button type="button" 
                    onclick='selectKBLI(${JSON.stringify(item)})' 
                    class="w-full px-3 sm:px-4 py-2 sm:py-3 text-left hover:bg-gray-100 dark:hover:bg-gray-700 transition border-b border-gray-100 dark:border-gray-700 last:border-0">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs sm:text-sm font-bold text-[#0a66c2] dark:text-[#0a66c2]">${item.code}</span>
                        </div>
                        <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 line-clamp-2">${item.description}</p>
                        <p class="text-[10px] sm:text-xs text-gray-500 mt-1">Sektor: ${item.sector}</p>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 text-xs mt-1 flex-shrink-0"></i>
                </div>
            </button>
        `;
    });
    
    html += '</div>';
    dropdown.innerHTML = html;
    dropdown.classList.remove('hidden');
}

function displayNoResults() {
    const dropdown = document.getElementById('kbli_dropdown');
    dropdown.innerHTML = `
        <div class="p-3 sm:p-4 text-center text-gray-500">
            <i class="fas fa-search mb-2 text-xl sm:text-2xl"></i>
            <p class="text-xs sm:text-sm">Tidak ada hasil ditemukan</p>
        </div>
    `;
    dropdown.classList.remove('hidden');
}

function selectKBLI(kbli) {
    selectedKBLI = kbli;
    
    // Set hidden inputs
    document.getElementById('kbli_code').value = kbli.code;
    document.getElementById('kbli_description').value = kbli.description;
    
    // Clear search input
    document.getElementById('kbli_search').value = '';
    
    // Hide dropdown
    document.getElementById('kbli_dropdown').classList.add('hidden');
    
    // Show selected KBLI
    showSelectedKBLI(kbli);
}

function showSelectedKBLI(kbli) {
    const selectedDiv = document.getElementById('kbli_selected');
    
    document.getElementById('selected_code').textContent = kbli.code;
    document.getElementById('selected_description').textContent = kbli.description;
    
    selectedDiv.classList.remove('hidden');
}

function clearKBLI() {
    selectedKBLI = null;
    document.getElementById('kbli_code').value = '';
    document.getElementById('kbli_description').value = '';
    document.getElementById('kbli_selected').classList.add('hidden');
    document.getElementById('kbli_search').value = '';
    document.getElementById('kbli_search').focus();
}

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('kbli_dropdown');
    const searchInput = document.getElementById('kbli_search');
    
    if (!dropdown.contains(e.target) && e.target !== searchInput) {
        dropdown.classList.add('hidden');
    }
});

// Phase 4.4 — Inline field validation with aria-describedby + success state
(function initInlineValidation() {
    const form = document.getElementById('applicationForm');
    if (!form) return;

    function getOrCreateMsg(field) {
        const msgId = field.id ? field.id + '-msg' : null;
        let msg = msgId ? document.getElementById(msgId) : null;
        if (!msg) {
            msg = document.createElement('p');
            msg.className = 'mt-1 text-xs hidden';
            if (msgId) msg.id = msgId;
            msg.setAttribute('aria-live', 'polite');
            field.parentElement.insertAdjacentElement('afterend', msg);
            if (msgId) field.setAttribute('aria-describedby', msgId);
        }
        return msg;
    }

    function validateField(field) {
        if (!field.willValidate || field.type === 'hidden') return;
        const msg = getOrCreateMsg(field);
        const valid = field.validity.valid;
        field.classList.toggle('border-red-500', !valid);
        field.classList.toggle('border-green-500', valid && field.value.length > 0);
        if (!valid) {
            let text = field.validationMessage;
            if (field.validity.valueMissing)   text = 'Kolom ini wajib diisi';
            if (field.validity.typeMismatch)   text = 'Format tidak valid';
            if (field.validity.tooShort)       text = 'Terlalu pendek (min ' + field.minLength + ' karakter)';
            if (field.validity.patternMismatch) text = 'Format tidak sesuai';
            msg.className = 'mt-1 text-xs text-red-600 flex items-center gap-1';
            msg.innerHTML = '<i class="fas fa-circle-exclamation" aria-hidden="true"></i> ' + text;
        } else if (field.value.length > 0) {
            msg.className = 'mt-1 text-xs text-green-600 flex items-center gap-1';
            msg.innerHTML = '<i class="fas fa-check-circle" aria-hidden="true"></i> Oke';
        } else {
            msg.className = 'mt-1 text-xs hidden';
            msg.innerHTML = '';
        }
    }

    form.addEventListener('blur', (e) => {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'SELECT' || e.target.tagName === 'TEXTAREA') {
            validateField(e.target);
        }
    }, true);

    form.addEventListener('input', (e) => {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
            if (!e.target.validity.valid || e.target.classList.contains('border-red-500')) {
                validateField(e.target);
            }
        }
    });
})();
</script>

