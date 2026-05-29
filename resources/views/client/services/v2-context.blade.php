@php
    $kbliSubtitle = trim((string) ($kbli->activities ?: $kbli->category ?: ''));
@endphp

<div x-data="contextForm('{{ $kbli->code }}', '{{ addslashes($kbli->description) }}')"> 

    {{-- ══ HERO ══ --}}
    <section class="portal-hero portal-accent-line relative overflow-hidden border-b border-[var(--border-subtle)]"
             style="background: linear-gradient(145deg, color-mix(in oklab, var(--client-primary) 82%, #041425) 0%, color-mix(in oklab, var(--client-primary) 52%, #02101d) 58%, #051523 100%); color:#fff;"
             aria-label="Konteks Bisnis">

        <div class="portal-glow-orb portal-glow-orb--tr hidden lg:block" aria-hidden="true"></div>

        <div class="max-w-[1400px] mx-auto px-4 lg:px-8 py-5 lg:py-7 relative z-10">
            <nav class="flex items-center gap-1.5 text-[11px] mb-4 flex-wrap text-white/70" aria-label="Breadcrumb">
                <a href="{{ route('client.services.index') }}" class="hover:text-white transition-colors">Katalog Perizinan</a>
                <i class="fas fa-chevron-right text-[9px] opacity-50"></i>
                <span class="text-white/90 font-semibold">{{ $kbli->code }}</span>
                <i class="fas fa-chevron-right text-[9px] opacity-50"></i>
                <span class="text-white/90 font-semibold">Konteks Bisnis</span>
            </nav>

            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-2 flex-wrap mb-2">
                        <span class="portal-eyebrow" style="background:rgba(255,255,255,0.15);color:rgba(255,255,255,0.92);border-color:rgba(255,255,255,0.22);">
                            <i class="fas fa-wand-magic-sparkles text-[9px]" aria-hidden="true"></i>
                            AI Recommendation
                        </span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-mono font-semibold"
                              style="background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.2);">
                            KBLI {{ $kbli->code }}
                        </span>
                        @if($kbli->sector)
                        <span class="text-[11px] text-white/70">Sektor {{ $kbli->sector }}</span>
                        @endif
                    </div>
                    <h1 class="text-xl sm:text-2xl font-bold leading-snug text-white">{{ $kbli->description }}</h1>
                    @if($kbliSubtitle !== '')
                    <div x-data="{ expanded: false }" class="mt-2 max-w-3xl">
                        <p class="text-sm leading-6 text-white/90 font-[450] transition-all"
                           :class="expanded ? '' : 'line-clamp-2'">{{ $kbliSubtitle }}</p>
                        @if(strlen($kbliSubtitle) > 140)
                        <button type="button" @click="expanded = !expanded"
                                class="mt-1 text-[11px] text-white/60 hover:text-white/90 underline underline-offset-2 transition-colors">
                            <span x-text="expanded ? 'Sembunyikan ↑' : 'Lihat selengkapnya ↓'"></span>
                        </button>
                        @endif
                    </div>
                    @endif
                </div>

                <div class="flex items-center gap-2.5 flex-shrink-0">
                    <div class="flex flex-col items-center gap-1 rounded-2xl px-4 py-3 min-w-[70px]" style="background:rgba(255,255,255,0.10);border:1px solid rgba(255,255,255,0.18);backdrop-filter:blur(8px);">
                        <i class="fas fa-list-ol text-[11px] text-white/55" aria-hidden="true"></i>
                        <p class="text-lg font-bold tabular-nums text-white leading-none">4</p>
                        <p class="text-[10px] text-white/65">Langkah</p>
                    </div>
                    <div class="flex flex-col items-center gap-1 rounded-2xl px-4 py-3 min-w-[70px]" style="background:rgba(255,255,255,0.10);border:1px solid rgba(255,255,255,0.18);backdrop-filter:blur(8px);">
                        <i class="fas fa-hand-holding-heart text-[11px] text-white/55" aria-hidden="true"></i>
                        <p class="text-lg font-bold text-white leading-none">100%</p>
                        <p class="text-[10px] text-white/65">Gratis</p>
                    </div>
                    <div class="flex flex-col items-center gap-1 rounded-2xl px-4 py-3 min-w-[70px]" style="background:rgba(255,255,255,0.10);border:1px solid rgba(255,255,255,0.18);backdrop-filter:blur(8px);">
                        <i class="fas fa-clock text-[11px] text-white/55" aria-hidden="true"></i>
                        <p class="text-lg font-bold text-white leading-none">5</p>
                        <p class="text-[10px] text-white/65">Menit</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ══ STEP PROGRESS ══ --}}
    <div class="bg-[var(--surface-elevated)] border-b border-[var(--border-subtle)]">
        <div class="max-w-[1400px] mx-auto px-4 lg:px-8 py-4">
            <div class="flex items-center gap-0 max-w-sm">
                <template x-for="(step, index) in steps" :key="index">
                    <div class="flex items-center flex-1">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-200"
                             :class="index < currentStep
                                 ? 'bg-[var(--client-primary)] text-white shadow-sm'
                                 : index === currentStep
                                     ? 'bg-[var(--client-primary)] text-white ring-4 ring-[var(--client-primary)]/30 scale-110 shadow-md'
                                     : 'bg-[var(--surface-cool)] text-[var(--text-tertiary)] border-2 border-[var(--border-subtle)]'">
                            <template x-if="index < currentStep"><i class="fas fa-check text-[10px]"></i></template>
                            <template x-if="index >= currentStep"><span x-text="index + 1"></span></template>
                        </div>
                        <div class="flex-1 h-0.5 mx-1.5 transition-all duration-300"
                             x-show="index < steps.length - 1"
                             :class="index < currentStep ? 'bg-[var(--client-primary)]' : 'bg-[var(--border-subtle)]'"></div>
                    </div>
                </template>
            </div>
            <p class="mt-2 text-xs font-semibold text-[var(--text-primary)]"
               x-text="'Langkah ' + (currentStep + 1) + ' dari ' + steps.length + ': ' + steps[currentStep]"></p>
        </div>
    </div>

    {{-- ══ FORM ══ --}}
    <form action="{{ route('client.services.context', $kbli->code) }}"
          method="POST"
          @submit.prevent="submitForm"
          class="max-w-[1400px] mx-auto px-4 lg:px-8 py-6 space-y-5">
        @csrf

        {{-- ══ NIB AUTO-FILL (above Step 1) ══ --}}
        <div x-show="currentStep === 0" x-transition>
            <div class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-2xl overflow-hidden mb-5">
                <div class="px-6 py-4 border-b border-[var(--border-subtle)] flex items-center gap-3">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg" style="background:rgba(var(--client-primary-rgb,59,130,246),0.12);color:var(--client-primary);">
                        <i class="fas fa-id-card text-xs" aria-hidden="true"></i>
                    </span>
                    <div class="flex-1">
                        <h2 class="text-sm font-bold text-[var(--text-primary)]">Auto-isi dari NIB <span class="ml-1 text-[10px] font-normal text-[var(--text-tertiary)] uppercase tracking-wide">Opsional</span></h2>
                        <p class="text-xs text-[var(--text-secondary)]">Masukkan NIB usaha Anda untuk mengisi formulir secara otomatis</p>
                    </div>
                </div>
                <div class="px-6 py-5">
                    <div class="flex gap-3">
                        <input type="text"
                               x-model="nibQuery"
                               @keydown.enter.prevent="lookupNIB()"
                               placeholder="Contoh: 1234567890123 atau nama perusahaan"
                               maxlength="200"
                               class="flex-1 px-3 py-2.5 text-sm rounded-xl border border-[var(--border-subtle)] bg-[var(--surface-cool)] text-[var(--text-primary)] placeholder-[var(--text-tertiary)] focus:ring-2 focus:ring-[var(--client-primary)]/40 focus:outline-none transition-all">
                        <button type="button"
                                @click="lookupNIB()"
                                :disabled="nibLoading || nibQuery.trim().length < 3"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white transition-all active:scale-[0.98] disabled:opacity-40 disabled:cursor-not-allowed"
                                style="background:var(--client-primary);">
                            <template x-if="!nibLoading">
                                <span><i class="fas fa-search text-xs mr-1"></i> Cari</span>
                            </template>
                            <template x-if="nibLoading">
                                <span><svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></span>
                            </template>
                        </button>
                    </div>
                    {{-- NIB Result --}}
                    <div x-show="nibResult && nibResult.found" x-transition class="mt-3 flex items-center gap-3 rounded-xl px-4 py-3 border" style="background:rgba(34,197,94,0.08);border-color:rgba(34,197,94,0.3);">
                        <i class="fas fa-circle-check text-green-500 flex-shrink-0"></i>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-[var(--text-primary)] truncate" x-text="nibResult?.data?.company_name || nibResult?.company_name || 'Data ditemukan'"></p>
                            <p class="text-[11px] text-[var(--text-secondary)]" x-text="'NIB: ' + (nibResult?.data?.nib || nibResult?.nib || '-')"></p>
                        </div>
                        <span class="text-[10px] font-semibold text-green-600 bg-green-50 px-2 py-0.5 rounded-full whitespace-nowrap">Terpasang</span>
                    </div>
                    <div x-show="nibError" x-transition class="mt-3 flex items-center gap-2 text-xs text-amber-700 rounded-xl px-4 py-2.5" style="background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.25);">
                        <i class="fas fa-triangle-exclamation flex-shrink-0"></i>
                        <span x-text="nibError"></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 1 --}}
        <div x-show="currentStep === 0" x-transition>
            <div class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-[var(--border-subtle)] flex items-center gap-3">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-[var(--client-primary-light)] text-[var(--client-primary)]">
                        <i class="fas fa-chart-pie text-xs" aria-hidden="true"></i>
                    </span>
                    <div>
                        <h2 class="text-sm font-bold text-[var(--text-primary)]">Skala Usaha</h2>
                        <p class="text-xs text-[var(--text-secondary)]">Pilih skala dan ukuran usaha Anda</p>
                    </div>
                </div>
                <div class="px-6 py-5 space-y-5">
                    <div>
                        <p class="text-xs font-semibold text-[var(--text-secondary)] mb-3 uppercase tracking-[0.12em]">Kategori Usaha <span class="text-red-500">*</span></p>
                        <div class="grid sm:grid-cols-2 gap-3">
                            @foreach([
                                ['mikro',    'Usaha Mikro',    'fa-seedling', 'Aset ≤ Rp 50 juta atau Omzet ≤ Rp 300 juta/tahun'],
                                ['kecil',    'Usaha Kecil',    'fa-store',    'Aset Rp 50 juta – Rp 500 juta'],
                                ['menengah', 'Usaha Menengah', 'fa-building', 'Aset Rp 500 juta – Rp 10 miliar'],
                                ['besar',    'Usaha Besar',    'fa-city',     'Aset > Rp 10 miliar'],
                            ] as [$val, $label, $icon, $desc])
                            <label class="relative flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all duration-150 active:scale-[0.98]"
                                   :class="formData.business_scale === '{{ $val }}'
                                       ? 'border-[var(--client-primary)] bg-[var(--client-primary)]/8 shadow-sm'
                                       : 'border-[var(--border-subtle)] hover:border-[var(--client-primary)]/50 bg-[var(--surface-cool)]'">
                                <input type="radio" name="business_scale" value="{{ $val }}" x-model="formData.business_scale" class="sr-only">
                                <span class="absolute top-2 right-2 w-4 h-4 rounded-full flex items-center justify-center transition-all duration-150"
                                      :class="formData.business_scale === '{{ $val }}' ? 'bg-[var(--client-primary)] opacity-100' : 'opacity-0'">
                                    <i class="fas fa-check text-white" style="font-size:8px;" aria-hidden="true"></i>
                                </span>
                                <span class="flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center transition-all"
                                      :class="formData.business_scale === '{{ $val }}'
                                          ? 'bg-[var(--client-primary)] text-white'
                                          : 'bg-[var(--surface-elevated)] text-[var(--text-tertiary)]'">
                                    <i class="fas {{ $icon }} text-xs" aria-hidden="true"></i>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-[var(--text-primary)]">{{ $label }}</p>
                                    <p class="text-xs text-[var(--text-secondary)] mt-0.5">{{ $desc }}</p>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1.5">Luas Tanah <span class="text-red-500">*</span></label>
                            <div class="flex rounded-xl overflow-hidden border border-[var(--border-subtle)] bg-[var(--surface-cool)] focus-within:ring-2 focus-within:ring-[var(--client-primary)]/40 focus-within:border-[var(--client-primary)]/40 transition-all">
                                <input type="number" name="land_area" x-model="formData.land_area" step="0.01" min="0" required placeholder="500"
                                       class="flex-1 px-3 py-2.5 text-sm bg-transparent text-[var(--text-primary)] placeholder-[var(--text-tertiary)] focus:outline-none min-w-0">
                                <span class="flex items-center px-3 text-xs font-semibold text-[var(--text-tertiary)] bg-[var(--surface-elevated)] border-l border-[var(--border-subtle)] whitespace-nowrap select-none">m²</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1.5">Luas Bangunan</label>
                            <div class="flex rounded-xl overflow-hidden border border-[var(--border-subtle)] bg-[var(--surface-cool)] focus-within:ring-2 focus-within:ring-[var(--client-primary)]/40 focus-within:border-[var(--client-primary)]/40 transition-all">
                                <input type="number" name="building_area" x-model="formData.building_area" step="0.01" min="0" placeholder="300"
                                       class="flex-1 px-3 py-2.5 text-sm bg-transparent text-[var(--text-primary)] placeholder-[var(--text-tertiary)] focus:outline-none min-w-0">
                                <span class="flex items-center px-3 text-xs font-semibold text-[var(--text-tertiary)] bg-[var(--surface-elevated)] border-l border-[var(--border-subtle)] whitespace-nowrap select-none">m²</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1.5">Jumlah Lantai</label>
                            <input type="number" name="number_of_floors" x-model="formData.number_of_floors" min="1" max="100" placeholder="Contoh: 2"
                                   class="w-full px-3 py-2.5 text-sm rounded-xl border border-[var(--border-subtle)] bg-[var(--surface-cool)] text-[var(--text-primary)] placeholder-[var(--text-tertiary)] focus:ring-2 focus:ring-[var(--client-primary)]/40 focus:outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1.5">Nilai Investasi</label>
                            <div class="flex rounded-xl overflow-hidden border border-[var(--border-subtle)] bg-[var(--surface-cool)] focus-within:ring-2 focus-within:ring-[var(--client-primary)]/40 focus-within:border-[var(--client-primary)]/40 transition-all">
                                <span class="flex items-center px-3 text-xs font-semibold text-[var(--text-tertiary)] bg-[var(--surface-elevated)] border-r border-[var(--border-subtle)] whitespace-nowrap select-none">Rp</span>
                                <input type="number" name="investment_value" x-model="formData.investment_value" step="1000000" min="0" placeholder="5.000.000.000"
                                       class="flex-1 px-3 py-2.5 text-sm bg-transparent text-[var(--text-primary)] placeholder-[var(--text-tertiary)] focus:outline-none min-w-0">
                            </div>
                            <p class="text-xs text-[var(--text-tertiary)] mt-1" x-show="formData.investment_value > 0" x-text="formatCurrency(formData.investment_value)"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 2 --}}
        <div x-show="currentStep === 1" x-transition>
            <div class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-[var(--border-subtle)] flex items-center gap-3">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-[var(--client-primary-light)] text-[var(--client-primary)]">
                        <i class="fas fa-map-location-dot text-xs" aria-hidden="true"></i>
                    </span>
                    <div>
                        <h2 class="text-sm font-bold text-[var(--text-primary)]">Lokasi Proyek</h2>
                        <p class="text-xs text-[var(--text-secondary)]">Wilayah tempat usaha beroperasi</p>
                    </div>
                </div>
                <div class="px-6 py-5 space-y-5">
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1.5">Provinsi <span class="text-red-500">*</span></label>
                            <input type="text" name="province" x-model="formData.province" required placeholder="Contoh: DKI Jakarta"
                                   class="w-full px-3 py-2.5 text-sm rounded-xl border border-[var(--border-subtle)] bg-[var(--surface-cool)] text-[var(--text-primary)] placeholder-[var(--text-tertiary)] focus:ring-2 focus:ring-[var(--client-primary)]/40 focus:outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1.5">Kota/Kabupaten <span class="text-red-500">*</span></label>
                            <input type="text" name="city" x-model="formData.city"
                                   @input.debounce.600ms="fetchSimbgHint(formData.city)"
                                   required placeholder="Contoh: Jakarta Selatan"
                                   class="w-full px-3 py-2.5 text-sm rounded-xl border border-[var(--border-subtle)] bg-[var(--surface-cool)] text-[var(--text-primary)] placeholder-[var(--text-tertiary)] focus:ring-2 focus:ring-[var(--client-primary)]/40 focus:outline-none transition-all">
                            {{-- SIMBG Hint --}}
                            <div x-show="simbgHint.loading" class="mt-2 flex items-center gap-2 text-xs text-[var(--text-tertiary)]">
                                <svg class="animate-spin h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Mengambil info PBG...
                            </div>
                            <div x-show="simbgHint.data && !simbgHint.loading" x-transition class="mt-2 rounded-xl px-4 py-3 border" style="background:rgba(59,130,246,0.07);border-color:rgba(59,130,246,0.2);">
                                <div class="flex items-center gap-2 mb-1">
                                    <i class="fas fa-building-columns text-blue-500 text-[11px]"></i>
                                    <span class="text-[11px] font-semibold text-[var(--text-primary)]">Info PBG/IMB — SIMBG</span>
                                </div>
                                <p class="text-xs text-[var(--text-secondary)] leading-5" x-text="simbgHint.summary"></p>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1.5">Kecamatan</label>
                            <input type="text" name="district" x-model="formData.district" placeholder="Contoh: Kebayoran Baru"
                                   class="w-full px-3 py-2.5 text-sm rounded-xl border border-[var(--border-subtle)] bg-[var(--surface-cool)] text-[var(--text-primary)] placeholder-[var(--text-tertiary)] focus:ring-2 focus:ring-[var(--client-primary)]/40 focus:outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1.5">Jenis Zona</label>
                            <select name="zone_type" x-model="formData.zone_type"
                                    class="w-full px-3 py-2.5 text-sm rounded-xl border border-[var(--border-subtle)] bg-[var(--surface-cool)] text-[var(--text-primary)] focus:ring-2 focus:ring-[var(--client-primary)]/40 focus:outline-none transition-all">
                                <option value="">Pilih Jenis Zona</option>
                                <option value="residential">Perumahan</option>
                                <option value="commercial">Komersial</option>
                                <option value="industrial">Industri</option>
                                <option value="mixed">Mixed Use</option>
                                <option value="special">Kawasan Khusus</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-[var(--text-secondary)] mb-3 uppercase tracking-[0.12em]">Kategori Lokasi <span class="text-red-500">*</span></p>
                        <div class="grid sm:grid-cols-3 gap-3">
                            @foreach([
                                ['perkotaan',       'Perkotaan',         'fa-city',     'Area komersial, pusat kota'],
                                ['pedesaan',        'Pedesaan',          'fa-tree',     'Area pertanian, desa'],
                                ['kawasan_industri','Kawasan Industri',  'fa-industry', 'Area pabrik, gudang'],
                            ] as [$val, $label, $icon, $desc])
                            <label class="relative flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all duration-150 active:scale-[0.98]"
                                   :class="formData.location_category === '{{ $val }}'
                                       ? 'border-[var(--client-primary)] bg-[var(--client-primary)]/8 shadow-sm'
                                       : 'border-[var(--border-subtle)] hover:border-[var(--client-primary)]/50 bg-[var(--surface-cool)]'">
                                <input type="radio" name="location_category" value="{{ $val }}" x-model="formData.location_category" required class="sr-only">
                                <span class="absolute top-2 right-2 w-4 h-4 rounded-full flex items-center justify-center transition-all duration-150"
                                      :class="formData.location_category === '{{ $val }}' ? 'bg-[var(--client-primary)] opacity-100' : 'opacity-0'">
                                    <i class="fas fa-check text-white" style="font-size:8px;" aria-hidden="true"></i>
                                </span>
                                <span class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center transition-all"
                                      :class="formData.location_category === '{{ $val }}'
                                          ? 'bg-[var(--client-primary)] text-white'
                                          : 'bg-[var(--surface-elevated)] text-[var(--text-tertiary)]'">
                                    <i class="fas {{ $icon }} text-xs" aria-hidden="true"></i>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-[var(--text-primary)]">{{ $label }}</p>
                                    <p class="text-xs text-[var(--text-secondary)] mt-0.5">{{ $desc }}</p>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 3 --}}
        <div x-show="currentStep === 2" x-transition>
            <div class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-[var(--border-subtle)] flex items-center gap-3">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-[var(--client-primary-light)] text-[var(--client-primary)]">
                        <i class="fas fa-leaf text-xs" aria-hidden="true"></i>
                    </span>
                    <div>
                        <h2 class="text-sm font-bold text-[var(--text-primary)]">Detail Bisnis & Lingkungan</h2>
                        <p class="text-xs text-[var(--text-secondary)]">Operasional dan dampak lingkungan usaha</p>
                    </div>
                </div>
                <div class="px-6 py-5 space-y-5">
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1.5">Jumlah Karyawan</label>
                            <input type="number" name="number_of_employees" x-model="formData.number_of_employees" min="0" placeholder="Contoh: 25"
                                   class="w-full px-3 py-2.5 text-sm rounded-xl border border-[var(--border-subtle)] bg-[var(--surface-cool)] text-[var(--text-primary)] placeholder-[var(--text-tertiary)] focus:ring-2 focus:ring-[var(--client-primary)]/40 focus:outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1.5">Kapasitas Produksi</label>
                            <input type="text" name="production_capacity" x-model="formData.production_capacity" placeholder="Contoh: 1000 unit/bulan"
                                   class="w-full px-3 py-2.5 text-sm rounded-xl border border-[var(--border-subtle)] bg-[var(--surface-cool)] text-[var(--text-primary)] placeholder-[var(--text-tertiary)] focus:ring-2 focus:ring-[var(--client-primary)]/40 focus:outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1.5">Target Omzet Tahunan</label>
                            <div class="flex rounded-xl overflow-hidden border border-[var(--border-subtle)] bg-[var(--surface-cool)] focus-within:ring-2 focus-within:ring-[var(--client-primary)]/40 focus-within:border-[var(--client-primary)]/40 transition-all">
                                <span class="flex items-center px-3 text-xs font-semibold text-[var(--text-tertiary)] bg-[var(--surface-elevated)] border-r border-[var(--border-subtle)] whitespace-nowrap select-none">Rp</span>
                                <input type="number" name="annual_revenue_target" x-model="formData.annual_revenue_target" step="1000000" min="0" placeholder="10.000.000.000"
                                       class="flex-1 px-3 py-2.5 text-sm bg-transparent text-[var(--text-primary)] placeholder-[var(--text-tertiary)] focus:outline-none min-w-0">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1.5">Dampak Lingkungan <span class="text-red-500">*</span></label>
                            <select name="environmental_impact" x-model="formData.environmental_impact" required
                                    class="w-full px-3 py-2.5 text-sm rounded-xl border border-[var(--border-subtle)] bg-[var(--surface-cool)] text-[var(--text-primary)] focus:ring-2 focus:ring-[var(--client-primary)]/40 focus:outline-none transition-all">
                                <option value="low">Rendah (Tidak ada limbah berbahaya)</option>
                                <option value="medium">Sedang (Limbah standar yang dikelola)</option>
                                <option value="high">Tinggi (Limbah B3 atau emisi signifikan)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1.5">Pengelolaan Limbah</label>
                            <select name="waste_management" x-model="formData.waste_management"
                                    class="w-full px-3 py-2.5 text-sm rounded-xl border border-[var(--border-subtle)] bg-[var(--surface-cool)] text-[var(--text-primary)] focus:ring-2 focus:ring-[var(--client-primary)]/40 focus:outline-none transition-all">
                                <option value="">Pilih Tingkat Pengelolaan</option>
                                <option value="minimal">Minimal (Sampah umum)</option>
                                <option value="standard">Standard (Pemilahan limbah)</option>
                                <option value="complex">Kompleks (IPAL, TPS B3)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1.5">Status Kepemilikan</label>
                            <select name="ownership_status" x-model="formData.ownership_status"
                                    class="w-full px-3 py-2.5 text-sm rounded-xl border border-[var(--border-subtle)] bg-[var(--surface-cool)] text-[var(--text-primary)] focus:ring-2 focus:ring-[var(--client-primary)]/40 focus:outline-none transition-all">
                                <option value="">Pilih Status</option>
                                <option value="owned">Milik Sendiri</option>
                                <option value="leased">Sewa</option>
                                <option value="partnership">Kerjasama</option>
                            </select>
                        </div>
                    </div>
                        {{-- BPJPH Halal Check (F&B KBLI only: prefix 10 or 11) --}}
                        <div x-show="kbliCode.startsWith('10') || kbliCode.startsWith('11')" x-transition
                             class="rounded-2xl border border-[var(--border-subtle)] overflow-hidden" style="background:rgba(251,191,36,0.04);">
                            <div class="px-4 py-3 border-b border-[var(--border-subtle)] flex items-center gap-2">
                                <i class="fas fa-star-and-crescent text-amber-500 text-xs"></i>
                                <span class="text-xs font-semibold text-[var(--text-primary)]">Cek Status Sertifikasi Halal</span>
                                <span class="ml-auto text-[10px] text-[var(--text-tertiary)] font-normal">Opsional — BPJPH</span>
                            </div>
                            <div class="px-4 py-4 space-y-3">
                                <div class="flex gap-3">
                                    <input type="text"
                                           x-model="bpjphState.query"
                                           @keydown.enter.prevent="checkBpjph(bpjphState.query)"
                                           placeholder="Nama produk atau perusahaan"
                                           maxlength="200"
                                           class="flex-1 px-3 py-2 text-sm rounded-xl border border-[var(--border-subtle)] bg-[var(--surface-cool)] text-[var(--text-primary)] placeholder-[var(--text-tertiary)] focus:ring-2 focus:ring-amber-400/40 focus:outline-none transition-all">
                                    <button type="button"
                                            @click="checkBpjph(bpjphState.query)"
                                            :disabled="bpjphState.loading || bpjphState.query.trim().length < 3"
                                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold transition-all active:scale-[0.98] disabled:opacity-40 disabled:cursor-not-allowed"
                                            style="background:rgba(245,158,11,0.15);color:#b45309;border:1px solid rgba(245,158,11,0.35);">
                                        <template x-if="!bpjphState.loading"><span><i class="fas fa-search text-xs"></i> Cek</span></template>
                                        <template x-if="bpjphState.loading"><svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></template>
                                    </button>
                                </div>
                                <div x-show="bpjphState.result" x-transition>
                                    <template x-if="bpjphState.result && bpjphState.result.found">
                                        <div class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-xs" style="background:rgba(34,197,94,0.08);border:1px solid rgba(34,197,94,0.25);">
                                            <i class="fas fa-circle-check text-green-500"></i>
                                            <span class="font-semibold text-green-700">Sertifikat Halal Ditemukan</span>
                                            <span class="ml-auto text-[var(--text-secondary)]" x-text="bpjphState.result?.data?.certificate_number || ''"></span>
                                        </div>
                                    </template>
                                    <template x-if="bpjphState.result && !bpjphState.result.found">
                                        <div class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-xs" style="background:rgba(239,68,68,0.07);border:1px solid rgba(239,68,68,0.2);">
                                            <i class="fas fa-circle-xmark text-red-400"></i>
                                            <span class="text-red-600">Tidak ditemukan di database BPJPH</span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    <label class="flex items-start gap-3 cursor-pointer group">
                        <input type="checkbox" name="near_protected_area" x-model="formData.near_protected_area" value="1"
                               class="mt-0.5 rounded border-[var(--border-subtle)] text-[var(--client-primary)] focus:ring-[var(--client-primary)]">
                        <span class="text-sm text-[var(--text-secondary)] group-hover:text-[var(--text-primary)] transition-colors">
                            Lokasi dekat dengan kawasan lindung / konservasi
                        </span>
                    </label>

                    <div>
                        <label class="block text-xs font-semibold text-[var(--text-secondary)] mb-1.5">Catatan Tambahan</label>
                        <textarea name="additional_notes" x-model="formData.additional_notes" rows="3"
                                  placeholder="Informasi tambahan yang relevan untuk perhitungan izin…"
                                  class="w-full px-3 py-2.5 text-sm rounded-xl border border-[var(--border-subtle)] bg-[var(--surface-cool)] text-[var(--text-primary)] placeholder-[var(--text-tertiary)] focus:ring-2 focus:ring-[var(--client-primary)]/40 focus:outline-none transition-all resize-none"></textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 4 --}}
        <div x-show="currentStep === 3" x-transition class="space-y-5">
            <div class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-[var(--border-subtle)] flex items-center gap-3">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-[var(--client-primary-light)] text-[var(--client-primary)]">
                        <i class="fas fa-bolt text-xs" aria-hidden="true"></i>
                    </span>
                    <div>
                        <h2 class="text-sm font-bold text-[var(--text-primary)]">Tingkat Urgensi & Konfirmasi</h2>
                        <p class="text-xs text-[var(--text-secondary)]">Seberapa cepat izin perlu diselesaikan</p>
                    </div>
                </div>
                <div class="px-6 py-5">
                    <div class="grid sm:grid-cols-2 gap-3">
                        @foreach([
                            ['standard', 'Standard',         'fa-hourglass-half', 'Waktu normal sesuai prosedur', ''],
                            ['rush',     'Rush / Prioritas', 'fa-rocket',         'Percepatan dengan biaya tambahan', '+50%'],
                        ] as [$val, $label, $icon, $desc, $badge])
                        <label class="relative flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all duration-150 active:scale-[0.98]"
                               :class="formData.urgency_level === '{{ $val }}'
                                   ? 'border-[var(--client-primary)] bg-[var(--client-primary)]/8 shadow-sm'
                                   : 'border-[var(--border-subtle)] hover:border-[var(--client-primary)]/50 bg-[var(--surface-cool)]'">
                            <input type="radio" name="urgency_level" value="{{ $val }}" x-model="formData.urgency_level" class="sr-only">
                            <span class="absolute top-2 right-2 w-4 h-4 rounded-full flex items-center justify-center transition-all duration-150"
                                  :class="formData.urgency_level === '{{ $val }}' ? 'bg-[var(--client-primary)] opacity-100' : 'opacity-0'">
                                <i class="fas fa-check text-white" style="font-size:8px;" aria-hidden="true"></i>
                            </span>
                            <span class="flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center transition-all"
                                  :class="formData.urgency_level === '{{ $val }}'
                                      ? 'bg-[var(--client-primary)] text-white'
                                      : 'bg-[var(--surface-elevated)] text-[var(--text-tertiary)]'">
                                <i class="fas {{ $icon }} text-xs" aria-hidden="true"></i>
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-[var(--text-primary)]">
                                    {{ $label }}
                                    @if($badge)
                                    <span class="ml-1 text-[10px] font-bold text-[var(--client-primary)]">{{ $badge }}</span>
                                    @endif
                                </p>
                                <p class="text-xs text-[var(--text-secondary)] mt-0.5">{{ $desc }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Summary card --}}
            <div class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-[var(--border-subtle)]">
                    <h3 class="text-sm font-bold text-[var(--text-primary)]">Ringkasan Data</h3>
                </div>
                <div class="px-6 py-2 divide-y divide-[var(--border-subtle)]">
                    <div class="flex justify-between py-2.5">
                        <span class="text-xs text-[var(--text-secondary)]">Skala Usaha</span>
                        <span class="text-xs font-semibold text-[var(--text-primary)] capitalize" x-text="formData.business_scale || '-'"></span>
                    </div>
                    <div class="flex justify-between py-2.5">
                        <span class="text-xs text-[var(--text-secondary)]">Luas Tanah</span>
                        <span class="text-xs font-semibold text-[var(--text-primary)]" x-text="formData.land_area ? formData.land_area + ' m²' : '-'"></span>
                    </div>
                    <div class="flex justify-between py-2.5">
                        <span class="text-xs text-[var(--text-secondary)]">Lokasi</span>
                        <span class="text-xs font-semibold text-[var(--text-primary)]" x-text="formData.city && formData.province ? formData.city + ', ' + formData.province : '-'"></span>
                    </div>
                    <div class="flex justify-between py-2.5">
                        <span class="text-xs text-[var(--text-secondary)]">Dampak Lingkungan</span>
                        <span class="text-xs font-semibold text-[var(--text-primary)] capitalize" x-text="formData.environmental_impact || '-'"></span>
                    </div>
                    <div class="flex justify-between py-2.5">
                        <span class="text-xs text-[var(--text-secondary)]">Urgensi</span>
                        <span class="text-xs font-semibold text-[var(--text-primary)] capitalize" x-text="formData.urgency_level || '-'"></span>
                    </div>
                </div>
            </div>

            {{-- JDIH Regulations --}}
            <div x-show="jdihRegs.items.length > 0" x-transition
                 class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-[var(--border-subtle)] flex items-center gap-3">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-purple-50 text-purple-600">
                        <i class="fas fa-scale-balanced text-xs" aria-hidden="true"></i>
                    </span>
                    <div>
                        <h3 class="text-sm font-bold text-[var(--text-primary)]">Regulasi Terkait</h3>
                        <p class="text-xs text-[var(--text-secondary)]">Peraturan yang relevan dengan KBLI <span x-text="kbliCode"></span></p>
                    </div>
                    <div x-show="jdihRegs.loading" class="ml-auto">
                        <svg class="animate-spin h-4 w-4 text-[var(--text-tertiary)]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </div>
                </div>
                <div class="divide-y divide-[var(--border-subtle)]">
                    <template x-for="(reg, i) in jdihRegs.items" :key="i">
                        <div class="px-6 py-3 flex items-start gap-3">
                            <span class="flex-shrink-0 inline-flex items-center justify-center w-6 h-6 rounded-full text-[10px] font-bold mt-0.5"
                                  style="background:rgba(147,51,234,0.1);color:#7c3aed;" x-text="i + 1"></span>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-[var(--text-primary)] leading-5" x-text="reg.title || reg.data?.title || 'Regulasi'"></p>
                                <p class="text-[11px] text-[var(--text-tertiary)] mt-0.5" x-text="reg.data?.regulation_number || reg.data?.year || ''"></p>
                            </div>
                            <a x-show="reg.source_url" :href="reg.source_url" target="_blank" rel="noopener noreferrer"
                               class="flex-shrink-0 text-[11px] text-[var(--client-primary)] hover:underline">
                                Lihat <i class="fas fa-arrow-up-right-from-square text-[9px]"></i>
                            </a>
                        </div>
                    </template>
                </div>
            </div>

            <div class="flex items-start gap-3 rounded-2xl border border-[var(--client-primary)]/20 bg-[var(--client-primary)]/5 px-5 py-4">
                <i class="fas fa-info-circle text-[var(--client-primary)] mt-0.5 flex-shrink-0" aria-hidden="true"></i>
                <p class="text-sm text-[var(--text-secondary)] leading-6">
                    <strong class="text-[var(--text-primary)]">Catatan:</strong> Data yang Anda berikan akan digunakan untuk menghitung estimasi biaya perizinan yang lebih akurat, termasuk biaya pemerintah dan jasa konsultan Bizmark. Estimasi final akan ditampilkan di halaman berikutnya.
                </p>
            </div>
        </div>

        {{-- Navigation --}}
        <div class="flex items-center justify-between gap-4 pt-1">
            <div>
                <button type="button" @click="prevStep" x-show="currentStep > 0"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all active:scale-[0.98] border border-[var(--border-subtle)] bg-[var(--surface-elevated)] text-[var(--text-secondary)] hover:text-[var(--text-primary)]">
                    <i class="fas fa-arrow-left text-xs"></i> Kembali
                </button>
                <a href="{{ route('client.services.show', $kbli->code) }}"
                   x-show="currentStep === 0"
                   class="inline-flex items-center gap-1.5 text-sm text-[var(--text-secondary)] hover:text-[var(--text-primary)] transition-colors">
                    <i class="fas fa-forward text-[10px]"></i> Lewati (Rekomendasi Umum)
                </a>
            </div>
            <div>
                <button type="button" @click="nextStep" x-show="currentStep < steps.length - 1"
                        :disabled="(currentStep === 0 && (!formData.business_scale || !formData.land_area)) ||
                                   (currentStep === 1 && (!formData.province || !formData.city || !formData.location_category))"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold text-white transition-all active:scale-[0.98] hover:opacity-90 disabled:opacity-40 disabled:cursor-not-allowed disabled:active:scale-100"
                        style="background:var(--client-primary);">
                    Lanjutkan <i class="fas fa-arrow-right text-xs"></i>
                </button>
                <button type="submit" x-show="currentStep === steps.length - 1" :disabled="isSubmitting"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold text-white transition-all active:scale-[0.98] hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed"
                        style="background:var(--client-primary);">
                    <span x-show="!isSubmitting">Dapatkan Rekomendasi <i class="fas fa-arrow-right text-xs"></i></span>
                    <span x-show="isSubmitting" class="inline-flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Memproses…
                    </span>
                </button>
            </div>
        </div>
    </form>

    {{-- Loading overlay --}}
    <div x-show="isSubmitting"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 bg-gray-900/95 z-50 flex items-center justify-center"
         style="display:none;">
        <div class="text-center max-w-md mx-auto px-6">
            <div class="relative inline-block mb-6">
                <div class="w-20 h-20 border-4 border-[var(--client-primary)]/20 border-t-[var(--client-primary)] rounded-full animate-spin"></div>
            </div>
            <h3 class="text-2xl font-semibold text-white mb-3">Menganalisis Data Anda</h3>
            <p class="text-base text-gray-300 mb-6" x-text="loadingMessages[loadingStep]"></p>
            <div class="max-w-xs mx-auto">
                <div class="h-1.5 bg-gray-800 rounded-full overflow-hidden">
                    <div class="h-full bg-[var(--client-primary)] animate-progress-infinite"></div>
                </div>
                <p class="text-sm text-gray-400 mt-3">Mohon tunggu sebentar</p>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes progress-infinite {
        0%   { transform: translateX(-100%) scaleX(0.3); }
        50%  { transform: translateX(50%)   scaleX(0.6); }
        100% { transform: translateX(200%)  scaleX(0.3); }
    }
    .animate-progress-infinite { animation: progress-infinite 2s ease-in-out infinite; }
</style>

<script>
function contextForm(kbliCode, kbliDescription) {
    return {
        kbliCode: kbliCode || '',
        kbliDescription: kbliDescription || '',
        currentStep: 0,
        isSubmitting: false,
        loadingStep: 0,
        steps: ['Skala Usaha', 'Lokasi Proyek', 'Detail Bisnis & Lingkungan', 'Urgensi & Konfirmasi'],
        loadingMessages: [
            'Menganalisis KBLI dan regulasi terkait',
            'Menghitung kompleksitas proyek',
            'Menyusun rekomendasi perizinan',
            'Menghitung estimasi biaya akurat',
            'Hampir selesai...',
        ],
        formData: {
            business_scale: '',
            land_area: '',
            building_area: '',
            number_of_floors: '',
            investment_value: '',
            province: '',
            city: '',
            district: '',
            zone_type: '',
            location_category: '',
            number_of_employees: '',
            production_capacity: '',
            annual_revenue_target: '',
            environmental_impact: 'low',
            waste_management: '',
            ownership_status: '',
            near_protected_area: false,
            additional_notes: '',
            urgency_level: 'standard',
        },
        // NIB auto-fill state
        nibQuery: '',
        nibLoading: false,
        nibResult: null,
        nibError: null,
        // SIMBG hint state
        simbgHint: { loading: false, data: null, summary: '' },
        // BPJPH halal check state
        bpjphState: { loading: false, result: null, query: '' },
        // JDIH regulations state
        jdihRegs: { loading: false, items: [] },
        nextStep() {
            if (this.validateCurrentStep()) {
                if (this.currentStep < this.steps.length - 1) {
                    this.currentStep++;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    // Load JDIH regulations when entering the final confirmation step
                    if (this.currentStep === 3) {
                        this.fetchJdihRegs();
                    }
                }
            }
        },
        prevStep() {
            if (this.currentStep > 0) {
                this.currentStep--;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },
        validateCurrentStep() {
            if (this.currentStep === 0) {
                if (!this.formData.business_scale) { alert('Mohon pilih skala usaha'); return false; }
                if (!this.formData.land_area || this.formData.land_area <= 0) { alert('Mohon isi luas tanah'); return false; }
            } else if (this.currentStep === 1) {
                if (!this.formData.province) { alert('Mohon isi provinsi'); return false; }
                if (!this.formData.city)     { alert('Mohon isi kota/kabupaten'); return false; }
                if (!this.formData.location_category) { alert('Mohon pilih kategori lokasi'); return false; }
            } else if (this.currentStep === 2) {
                if (!this.formData.environmental_impact) { alert('Mohon pilih dampak lingkungan'); return false; }
            }
            return true;
        },
        submitForm(event) {
            if (!this.validateCurrentStep()) return;
            this.isSubmitting = true;
            const loadingInterval = setInterval(() => {
                if (this.loadingStep < this.loadingMessages.length - 1) this.loadingStep++;
            }, 2000);
            setTimeout(() => { event.target.submit(); }, 500);
        },
        formatCurrency(value) {
            if (!value) return '';
            const b = value / 1_000_000_000;
            if (b >= 1) return 'Rp ' + b.toFixed(2) + ' Miliar';
            return 'Rp ' + (value / 1_000_000).toFixed(2) + ' Juta';
        },
        async lookupNIB() {
            const q = this.nibQuery.trim();
            if (q.length < 3) return;
            this.nibLoading = true;
            this.nibResult = null;
            this.nibError = null;
            try {
                const res = await fetch('/api/civic/nib-lookup?q=' + encodeURIComponent(q), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                const data = await res.json();
                this.nibResult = data;
                if (data && data.found) {
                    // Auto-fill form fields from NIB data
                    const d = data.data || data;
                    if (d.province)       this.formData.province = d.province;
                    if (d.city)           this.formData.city = d.city;
                    if (d.business_scale) this.formData.business_scale = d.business_scale;
                } else {
                    this.nibError = 'NIB/nama tidak ditemukan di OSS. Silakan isi formulir secara manual.';
                }
            } catch (e) {
                this.nibError = 'Layanan OSS-NIB tidak tersedia saat ini. Silakan isi formulir secara manual.';
            } finally {
                this.nibLoading = false;
            }
        },
        async fetchSimbgHint(city) {
            const q = (city || '').trim();
            if (q.length < 3) return;
            this.simbgHint = { loading: true, data: null, summary: '' };
            try {
                const res = await fetch('/api/civic/simbg-hints?q=' + encodeURIComponent(q), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data && data.found !== false) {
                    const d = data.data || data;
                    const parts = [];
                    if (d.portal_name) parts.push(d.portal_name);
                    if (d.processing_days) parts.push('Proses PBG ±' + d.processing_days + ' hari kerja');
                    if (d.requirements_count) parts.push(d.requirements_count + ' persyaratan');
                    this.simbgHint = { loading: false, data: d, summary: parts.join(' · ') || 'Info PBG tersedia untuk wilayah ini.' };
                } else {
                    this.simbgHint = { loading: false, data: null, summary: '' };
                }
            } catch (e) {
                this.simbgHint = { loading: false, data: null, summary: '' };
            }
        },
        async checkBpjph(name) {
            const q = (name || '').trim();
            if (q.length < 3) return;
            this.bpjphState.loading = true;
            this.bpjphState.result = null;
            try {
                const res = await fetch('/api/civic/bpjph-check?q=' + encodeURIComponent(q), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                this.bpjphState.result = await res.json();
            } catch (e) {
                this.bpjphState.result = { found: false };
            } finally {
                this.bpjphState.loading = false;
            }
        },
        async fetchJdihRegs() {
            if (!this.kbliDescription && !this.kbliCode) return;
            this.jdihRegs = { loading: true, items: [] };
            try {
                const keyword = encodeURIComponent(this.kbliDescription || this.kbliCode);
                const res = await fetch('/api/civic/jdih-search?q=' + keyword + '&type=pp', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                const data = await res.json();
                const items = Array.isArray(data) ? data : (data.results || data.data || []);
                this.jdihRegs = { loading: false, items: items.filter(r => r && (r.found !== false)).slice(0, 5) };
            } catch (e) {
                this.jdihRegs = { loading: false, items: [] };
            }
        },
    };
}
</script>
