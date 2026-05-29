@extends('consultation.layout')

@section('meta_title', 'Estimasi Biaya Perizinan - Bizmark.ID')
@section('meta_description', 'Dapatkan estimasi biaya perizinan usaha Anda dengan AI analysis. Pilih jenis usaha (KBLI), isi informasi bisnis, dan terima estimasi biaya instan dengan rincian lengkap.')
@section('meta_keywords', 'estimasi biaya perizinan, kalkulator biaya izin, biaya pengurusan izin, konsultasi perizinan online, KBLI search')

@push('styles')
<!-- BreadcrumbList Structured Data -->
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        {
            "@@type": "ListItem",
            "position": 1,
            "name": "Beranda",
            "item": "{{ url('/') }}"
        },
        {
            "@@type": "ListItem",
            "position": 2,
            "name": "Estimasi Biaya Perizinan",
            "item": "{{ url()->current() }}"
        }
    ]
}
</script>
@endpush

@section('content')
<div class="min-h-screen consultation-section" style="padding-top: 5rem;">
    <!-- Breadcrumb -->
    <div style="background: var(--surface); border-bottom: 1px solid var(--border-light);">
        <div class="container-wide py-4">
            <nav aria-label="Breadcrumb" class="text-xs" style="color: var(--text-tertiary);">
                <a href="{{ url('/') }}" style="color: var(--text-secondary);">Beranda</a>
                <span class="mx-2">/</span>
                <span>Estimasi Biaya Perizinan</span>
            </nav>
        </div>
    </div>

    <!-- Header Section -->
    <div style="background: var(--surface); border-bottom: 1px solid var(--border-light);">
        <div class="container-wide py-12 md:py-16">
            <div class="max-w-4xl mx-auto text-center">
                <!-- Badge -->
                <span class="eyebrow mb-4 inline-flex items-center gap-2" style="color: var(--tools);">
                    <i class="fas fa-calculator"></i>
                    AI-Powered Estimation
                </span>
                
                <h1 class="display-lg mb-4" style="font-size: clamp(2rem, 4vw, 3rem);">
                    Estimasi Biaya Perizinan
                </h1>
                <p class="text-lg leading-relaxed max-w-2xl mx-auto mb-8" style="color: var(--text-secondary);">
                    Dapatkan estimasi biaya perizinan usaha Anda dengan AI analysis.<br>
                    Proses cepat, transparan, dan akurat berdasarkan jenis usaha dan kompleksitas perizinan.
                </p>
                <div class="flex items-center justify-center gap-6 text-sm" style="color: var(--text-secondary);">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-robot" style="color: var(--color-accent);"></i>
                        <span>AI-Powered</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-bolt" style="color: var(--color-secondary);"></i>
                        <span>Instant Result</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-lock" style="color: var(--color-success);"></i>
                        <span>Data Aman</span>
                    </div>
                </div>
                
                <!-- Recommendation Box -->
                <div class="mt-8 p-5 rounded-2xl" style="background: linear-gradient(135deg, rgba(14, 165, 233, 0.08) 0%, rgba(168, 85, 247, 0.08) 100%); border: 1px solid var(--border-light);">
                    <h3 class="font-bold text-base mb-3 flex items-center gap-2" style="color: var(--text-primary);">
                        <i class="fas fa-compass" style="color: var(--color-accent);"></i>
                        Pilih Layanan yang Tepat
                    </h3>
                    
                    <div class="space-y-3">
                        <!-- This page recommendation -->
                        <div class="p-3 rounded-xl flex items-start gap-3" style="background: rgba(22, 163, 74, 0.08); border: 1px solid rgba(22, 163, 74, 0.2);">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background: var(--color-success); color: white;">
                                <i class="fas fa-check text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold" style="color: var(--color-success);">Anda di halaman yang tepat jika:</p>
                                <ul class="text-xs mt-1 space-y-0.5" style="color: var(--text-secondary);">
                                    <li>• Sudah tahu KBLI / jenis usaha yang akan dijalankan</li>
                                    <li>• Ingin mengetahui <strong>berapa biaya</strong> pengurusan izin</li>
                                    <li>• Butuh estimasi cepat untuk budgeting</li>
                                </ul>
                            </div>
                        </div>
                        
                        <!-- Alternative recommendation -->
                        <div class="p-3 rounded-xl flex items-start gap-3" style="background: rgba(14, 165, 233, 0.05); border: 1px solid rgba(14, 165, 233, 0.15);">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background: var(--color-accent); color: white;">
                                <i class="fas fa-robot text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold" style="color: var(--color-accent);">Gunakan Analisis Perizinan Gratis jika:</p>
                                <ul class="text-xs mt-1 space-y-0.5" style="color: var(--text-secondary);">
                                    <li>• Belum yakin <strong>izin apa saja</strong> yang dibutuhkan</li>
                                    <li>• Ingin AI menganalisis kebutuhan perizinan Anda</li>
                                    <li>• Butuh rekomendasi jadwal dan instansi terkait</li>
                                </ul>
                                <a href="/konsultasi-gratis" class="inline-flex items-center gap-2 mt-2 text-sm font-semibold hover:underline" style="color: var(--color-accent);">
                                    Coba Analisis Gratis <i class="fas fa-arrow-right text-xs"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Section -->
    <div class="container-wide py-12">
        <div class="max-w-4xl mx-auto">
            <div class="form-card overflow-hidden">
                
                <form id="consultation-form" x-data="consultationForm()" @submit.prevent="submitForm">
                    
                    <!-- Step 1: KBLI Selection -->
                    <div class="p-8" style="border-bottom: 1px solid var(--border-light);">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="flex items-center justify-center w-10 h-10 rounded-full font-bold" style="background: rgba(14, 165, 233, 0.1); color: var(--color-accent);">
                                1
                            </div>
                            <h2 class="text-2xl font-bold" style="color: var(--text-primary);">
                                Pilih Jenis Usaha (KBLI)
                            </h2>
                        </div>

                        <!-- KBLI Autocomplete Component -->
                        <div class="relative" x-data="kbliAutocomplete()" @click.away="showResults = false">
                            <label class="form-label">
                                Cari KBLI <span style="color: #dc2626;">*</span>
                            </label>
                            
                            <div class="relative">
                                <input 
                                    type="text" 
                                    x-model="search"
                                    @input.debounce.300ms="searchKBLI"
                                    @focus="showResults = results.length > 0"
                                    @keydown.down.prevent="navigateDown"
                                    @keydown.up.prevent="navigateUp"
                                    @keydown.enter.prevent="selectHighlighted"
                                    @keydown.escape="showResults = false"
                                    placeholder="Ketik jenis usaha Anda, contoh: restoran, toko, manufaktur..."
                                    class="form-input"
                                    :class="{ 'border-red-500': error }"
                                    style="padding-right: 2.5rem;"
                                >
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fas fa-search" style="color: var(--text-tertiary);" x-show="!loading"></i>
                                    <i class="fas fa-spinner fa-spin" style="color: var(--color-accent);" x-show="loading"></i>
                                </div>
                            </div>

                            <!-- Selected KBLI Display -->
                            <div x-show="selectedKBLI" class="mt-3 p-4 rounded-lg" style="background: rgba(14, 165, 233, 0.05); border: 1px solid rgba(14, 165, 233, 0.2);">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="font-mono font-bold" style="color: var(--color-accent);" x-text="selectedKBLI?.code"></span>
                                            <span class="px-2 py-0.5 text-xs font-medium rounded" style="background: rgba(14, 165, 233, 0.1); color: var(--color-accent);" x-text="selectedKBLI?.complexity_level"></span>
                                        </div>
                                        <p class="text-sm font-medium" style="color: var(--text-primary);" x-text="selectedKBLI?.description"></p>
                                        <p class="text-xs mt-1" style="color: var(--text-secondary);" x-text="'Kategori: ' + selectedKBLI?.category"></p>
                                    </div>
                                    <button
                                        type="button"
                                        @click="clearSelection"
                                        @mouseenter="$el.style.color='#dc2626'"
                                        @mouseleave="$el.style.color='var(--text-tertiary)'"
                                        class="ml-3 transition"
                                        style="color: var(--text-tertiary);"
                                    >
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Autocomplete Dropdown -->
                            <div 
                                x-show="showResults && results.length > 0"
                                x-transition
                                class="absolute z-50 w-full mt-2 rounded-lg shadow-xl max-h-80 overflow-y-auto"
                                style="background: var(--surface); border: 1px solid var(--border-medium);"
                            >
                                <template x-for="(kbli, index) in results" :key="kbli.code">
                                    <button
                                        type="button"
                                        @click="selectKBLI(kbli)"
                                        @mouseenter="$el.style.background='var(--surface-warm)'"
                                        @mouseleave="$el.style.background='var(--surface)'"
                                        class="w-full px-4 py-3 text-left transition"
                                        :style="highlightedIndex === index ? 'background: rgba(14, 165, 233, 0.05)' : ''"
                                        style="border-bottom: 1px solid var(--border-light);"
                                    >
                                        <div class="flex items-start gap-3">
                                            <span class="font-mono text-sm font-bold mt-0.5" style="color: var(--color-accent);" x-text="kbli.code"></span>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium truncate" style="color: var(--text-primary);" x-text="kbli.description"></p>
                                                <p class="text-xs mt-0.5" style="color: var(--text-tertiary);" x-text="kbli.category"></p>
                                            </div>
                                            <span 
                                                class="px-2 py-0.5 text-xs font-medium rounded shrink-0"
                                                :class="{
                                                    'bg-green-100 text-green-700': kbli.complexity_level === 'low',
                                                    'bg-yellow-100 text-yellow-700': kbli.complexity_level === 'medium',
                                                    'bg-red-100 text-red-700': kbli.complexity_level === 'high'
                                                }"
                                                x-text="kbli.complexity_level"
                                            ></span>
                                        </div>
                                    </button>
                                </template>
                            </div>

                            <!-- Error Message -->
                            <p x-show="error" class="mt-2 text-sm" style="color: #dc2626;" x-text="error"></p>
                            
                            <!-- No Results Message -->
                            <p x-show="!loading && search.length >= 2 && results.length === 0 && !selectedKBLI" class="mt-2 text-sm" style="color: var(--text-tertiary);">
                                <i class="fas fa-info-circle mr-1"></i>
                                Tidak ada hasil. Coba kata kunci lain.
                            </p>

                            <!-- Popular KBLI Suggestions -->
                            <div x-show="!selectedKBLI && search.length === 0" class="mt-4">
                                <p class="text-sm font-medium mb-2" style="color: var(--text-secondary);">
                                    <i class="fas fa-fire mr-1" style="color: var(--color-secondary);"></i>
                                    KBLI Populer:
                                </p>
                                <div class="flex flex-wrap gap-2">
                                    <button
                                        type="button"
                                        @click="search = 'restoran'; searchKBLI()"
                                        @mouseenter="$el.style.background='rgba(14, 165, 233, 0.1)'; $el.style.borderColor='var(--color-accent)'"
                                        @mouseleave="$el.style.background='var(--surface-warm)'; $el.style.borderColor='var(--border-light)'"
                                        class="px-3 py-1.5 text-sm rounded-lg transition"
                                        style="background: var(--surface-warm); color: var(--text-secondary); border: 1px solid var(--border-light);"
                                    >
                                        Restoran
                                    </button>
                                    <button
                                        type="button"
                                        @click="search = 'toko'; searchKBLI()"
                                        @mouseenter="$el.style.background='rgba(14, 165, 233, 0.1)'; $el.style.borderColor='var(--color-accent)'"
                                        @mouseleave="$el.style.background='var(--surface-warm)'; $el.style.borderColor='var(--border-light)'"
                                        class="px-3 py-1.5 text-sm rounded-lg transition"
                                        style="background: var(--surface-warm); color: var(--text-secondary); border: 1px solid var(--border-light);"
                                    >
                                        Toko
                                    </button>
                                    <button
                                        type="button"
                                        @click="search = 'konstruksi'; searchKBLI()"
                                        @mouseenter="$el.style.background='rgba(14, 165, 233, 0.1)'; $el.style.borderColor='var(--color-accent)'"
                                        @mouseleave="$el.style.background='var(--surface-warm)'; $el.style.borderColor='var(--border-light)'"
                                        class="px-3 py-1.5 text-sm rounded-lg transition"
                                        style="background: var(--surface-warm); color: var(--text-secondary); border: 1px solid var(--border-light);"
                                    >
                                        Konstruksi
                                    </button>
                                    <button
                                        type="button"
                                        @click="search = 'manufaktur'; searchKBLI()"
                                        @mouseenter="$el.style.background='rgba(14, 165, 233, 0.1)'; $el.style.borderColor='var(--color-accent)'"
                                        @mouseleave="$el.style.background='var(--surface-warm)'; $el.style.borderColor='var(--border-light)'"
                                        class="px-3 py-1.5 text-sm rounded-lg transition"
                                        style="background: var(--surface-warm); color: var(--text-secondary); border: 1px solid var(--border-light);"
                                    >
                                        Manufaktur
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Business Information -->
                    <div class="p-8" style="border-bottom: 1px solid var(--border-light);" x-show="selectedKBLI">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="flex items-center justify-center w-10 h-10 rounded-full font-bold" style="background: rgba(14, 165, 233, 0.1); color: var(--color-accent);">
                                2
                            </div>
                            <h2 class="text-2xl font-bold" style="color: var(--text-primary);">
                                Informasi Bisnis
                            </h2>
                        </div>

                        <div class="mb-8 rounded-2xl p-5" style="border: 1px solid rgba(14, 165, 233, 0.2); background: linear-gradient(135deg, rgba(14, 165, 233, 0.06) 0%, rgba(249, 115, 22, 0.06) 100%);">
                            <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold flex items-center gap-2" style="color: var(--text-primary);">
                                        <i class="fas fa-diagram-project" style="color: var(--color-accent);"></i>
                                        Aktivitas Usaha Tambahan
                                    </h3>
                                    <p class="mt-1 text-sm" style="color: var(--text-secondary);">
                                        Opsional. Tambahkan sampai 3 aktivitas lain agar AI bisa memberi konteks regulasi per aktivitas, bukan hanya KBLI utama.
                                    </p>
                                </div>
                                <button type="button"
                                        @click="addAdditionalActivity()"
                                        :disabled="formData.additional_activities.length >= 3"
                                        class="inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold text-white disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                        style="background: var(--color-accent);">
                                    <i class="fas fa-plus"></i>
                                    Tambah Aktivitas
                                </button>
                            </div>

                            <div x-show="formData.additional_activities.length === 0" class="mt-4 rounded-xl border border-dashed px-4 py-5 text-sm text-center" style="border-color: var(--border-medium); color: var(--text-secondary); background: var(--surface);">
                                Belum ada aktivitas tambahan. KBLI utama tetap dianalisis otomatis.
                            </div>

                            <div class="mt-4 space-y-4" x-show="formData.additional_activities.length > 0">
                                <template x-for="(activity, index) in formData.additional_activities" :key="'activity-' + index">
                                    <div class="rounded-xl p-4 shadow-sm" style="background: var(--surface); border: 1px solid var(--border-light);">
                                        <div class="flex items-center justify-between gap-3 mb-4">
                                            <div>
                                                <p class="text-sm font-semibold" style="color: var(--text-primary);" x-text="`Aktivitas Tambahan ${index + 1}`"></p>
                                                <p class="text-xs" style="color: var(--text-secondary);">Isi KBLI 5 digit. Deskripsi bisa dikosongkan jika kode valid.</p>
                                            </div>
                                            <button type="button"
                                                    @click="removeAdditionalActivity(index)"
                                                    class="inline-flex items-center gap-2 rounded-lg border px-3 py-2 text-xs font-semibold transition-colors"
                                                    style="border-color: #fecaca; color: #dc2626; background: #fff7f7;">
                                                <i class="fas fa-trash"></i>
                                                Hapus
                                            </button>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="form-label">Kode KBLI</label>
                                                <input type="text"
                                                       x-model="activity.kbli_code"
                                                       maxlength="5"
                                                       inputmode="numeric"
                                                       pattern="[0-9]{5}"
                                                       placeholder="Contoh: 56101"
                                                       class="form-input">
                                            </div>
                                            <div>
                                                <label class="form-label">Deskripsi Aktivitas</label>
                                                <input type="text"
                                                       x-model="activity.description"
                                                       placeholder="Opsional jika kode KBLI sudah spesifik"
                                                       class="form-input">
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Business Size -->
                            <div>
                                <label class="form-label">
                                    Skala Usaha <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    x-model="formData.business_size"
                                    @change="calculateQuickEstimate"
                                    class="form-input form-select"
                                    required
                                >
                                    <option value="">Pilih skala usaha...</option>
                                    <option value="micro">Usaha Mikro (Omzet ≤ 300 juta/tahun, Karyawan ≤ 4 orang)</option>
                                    <option value="small">Usaha Kecil (Omzet 300 juta - 2.5 miliar/tahun, Karyawan 5-19 orang)</option>
                                    <option value="medium">Usaha Menengah (Omzet 2.5 - 50 miliar/tahun, Karyawan 20-99 orang)</option>
                                    <option value="large">Usaha Besar (Omzet > 50 miliar/tahun, Karyawan ≥ 100 orang)</option>
                                </select>
                                <p class="mt-1 text-xs" style="color: var(--text-secondary);">
                                    <i class="fas fa-chart-line mr-1"></i>
                                    Sesuai UU No. 20 Tahun 2008 tentang UMKM
                                </p>
                            </div>

                            <!-- Zone/Location Type -->
                            <div>
                                <label class="form-label">
                                    Zona/Kawasan Lokasi <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    x-model="formData.location_type"
                                    @change="calculateQuickEstimate"
                                    class="form-input form-select"
                                    required
                                >
                                    <option value="">Pilih zona/kawasan lokasi...</option>
                                    <option value="commercial">Kawasan Komersial/Bisnis</option>
                                    <option value="industrial">Kawasan Industri</option>
                                    <option value="residential">Kawasan Perumahan/Pemukiman</option>
                                    <option value="mixed_use">Kawasan Campuran (Mixed-Use)</option>
                                    <option value="special_economic">Kawasan Ekonomi Khusus (KEK)</option>
                                    <option value="rural_agricultural">Kawasan Pedesaan/Pertanian</option>
                                    <option value="tourism">Kawasan Pariwisata</option>
                                    <option value="educational">Kawasan Pendidikan</option>
                                </select>
                                <p class="mt-1 text-xs" style="color: var(--text-secondary);">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Pilih sesuai dengan zona/peruntukan lokasi usaha Anda
                                </p>
                            </div>

                            <!-- Geographic Region -->
                            <div>
                                <label class="form-label">
                                    Wilayah Geografis <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    x-model="formData.geographic_region"
                                    @change="calculateQuickEstimate"
                                    class="form-input form-select"
                                    required
                                >
                                    <option value="">Pilih wilayah geografis...</option>
                                    <option value="jakarta_capital">DKI Jakarta</option>
                                    <option value="java_major_cities">Kota Besar Jawa (Surabaya, Bandung, Semarang)</option>
                                    <option value="java_medium_cities">Kota Menengah Jawa</option>
                                    <option value="java_small_cities">Kota Kecil/Kabupaten Jawa</option>
                                    <option value="bali_lombok">Bali & Lombok</option>
                                    <option value="sumatra_major">Kota Besar Sumatera (Medan, Palembang, Pekanbaru)</option>
                                    <option value="sumatra_others">Kota Lain Sumatera</option>
                                    <option value="kalimantan_major">Kota Besar Kalimantan (Balikpapan, Pontianak)</option>
                                    <option value="kalimantan_others">Kota Lain Kalimantan</option>
                                    <option value="sulawesi_major">Kota Besar Sulawesi (Makassar, Manado)</option>
                                    <option value="sulawesi_others">Kota Lain Sulawesi</option>
                                    <option value="eastern_indonesia">Indonesia Timur (Papua, Maluku, NTT)</option>
                                    <option value="border_areas">Wilayah Perbatasan</option>
                                </select>
                                <p class="mt-1 text-xs" style="color: var(--text-secondary);">
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    Mempengaruhi kompleksitas perizinan dan biaya operasional
                                </p>
                            </div>

                            <!-- Specific Location (City/Regency) -->
                            <div>
                                <label class="form-label">
                                    Kota/Kabupaten <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    x-model="formData.location"
                                    placeholder="Contoh: Jakarta Selatan, Kab. Bandung, Kota Surabaya..."
                                    class="form-input"
                                    required
                                >
                                <p class="mt-1 text-xs" style="color: var(--text-secondary);">
                                    <i class="fas fa-building mr-1"></i>
                                    Untuk peraturan daerah spesifik dan retribusi lokal
                                </p>
                            </div>

                            <!-- Investment Level -->
                            <div>
                                <label class="form-label">
                                    Total Nilai Investasi <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    x-model="formData.investment_level"
                                    @change="calculateQuickEstimate"
                                    class="form-input form-select"
                                    required
                                >
                                    <option value="">Pilih nilai investasi...</option>
                                    <option value="under_100m">< Rp 100 Juta</option>
                                    <option value="100m_500m">Rp 100 Juta - 500 Juta</option>
                                    <option value="500m_2b">Rp 500 Juta - 2 Miliar</option>
                                    <option value="2b_10b">Rp 2 Miliar - 10 Miliar</option>
                                    <option value="10b_50b">Rp 10 Miliar - 50 Miliar</option>
                                    <option value="above_50b">Rp 50 Miliar keatas</option>
                                </select>
                                <p class="mt-1 text-xs" style="color: var(--text-secondary);">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Biaya perizinan umumnya 8-12% dari total investasi
                                </p>
                            </div>

                            <!-- Business Entity Type -->
                            <div>
                                <label class="form-label">
                                    Bentuk Badan Usaha <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    x-model="formData.entity_type"
                                    @change="calculateQuickEstimate"
                                    class="form-input form-select"
                                    required
                                >
                                    <option value="">Pilih bentuk badan usaha...</option>
                                    <option value="individual">Perorangan (Tidak Berbadan Hukum)</option>
                                    <option value="cv">CV (Comanditaire Vennootschap)</option>
                                    <option value="firma">Firma</option>
                                    <option value="pt">PT (Perseroan Terbatas)</option>
                                    <option value="pt_pma">PT PMA (Penanaman Modal Asing)</option>
                                    <option value="persero">Persero (BUMN)</option>
                                    <option value="perum">Perum (BUMN)</option>
                                    <option value="koperasi">Koperasi</option>
                                    <option value="yayasan">Yayasan</option>
                                    <option value="perkumpulan">Perkumpulan</option>
                                    <option value="bumn">BUMN/BUMD</option>
                                    <option value="foreign_rep">Kantor Perwakilan Asing</option>
                                </select>
                                <p class="mt-1 text-xs" style="color: var(--text-secondary);">
                                    <i class="fas fa-building mr-1"></i>
                                    Mempengaruhi jenis dokumen dan persyaratan perizinan
                                </p>
                            </div>

                            <!-- Employee Count -->
                            <div>
                                <label class="form-label">
                                    Jumlah Karyawan (Perkiraan)
                                </label>
                                <input 
                                    type="number" 
                                    x-model="formData.employee_count"
                                    placeholder="0 = belum ada karyawan"
                                    min="0"
                                    max="10000"
                                    class="form-input"
                                >
                                <p class="mt-1 text-xs" style="color: var(--text-secondary);">
                                    <i class="fas fa-users mr-1"></i>
                                    Termasuk owner/pendiri, mempengaruhi persyaratan ketenagakerjaan
                                </p>
                            </div>

                            <!-- Entity/Business Name -->
                            <div class="mb-6">
                                <label class="form-label">
                                    Nama Pengusul/Entitas <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    x-model="formData.applicant_name"
                                    placeholder="Contoh: PT Indovido Jaya, CV Maju Bersama, atau Budi Santoso"
                                    class="form-input"
                                    required
                                >
                                <p class="mt-1 text-xs" style="color: var(--text-secondary);">
                                    <i class="fas fa-building mr-1"></i>
                                    Nama badan usaha jika sudah ada, atau nama individu pengusul
                                </p>
                            </div>

                            <!-- Optional: Email for follow-up -->
                            <div class="mb-6">
                                <label class="form-label">
                                    Email (Opsional)
                                </label>
                                <input 
                                    type="email" 
                                    x-model="formData.applicant_email"
                                    placeholder="Contoh: info@perusahaan.com atau email.pribadi@gmail.com"
                                    class="form-input"
                                >
                                <p class="mt-1 text-xs" style="color: var(--text-secondary);">
                                    <i class="fas fa-envelope mr-1"></i>
                                    Email untuk mengirimkan hasil estimasi lengkap dan tindak lanjut
                                </p>
                            </div>

                            <!-- Contact Info -->
                            <div>
                                <label class="form-label">
                                    Nomor WhatsApp <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="tel" 
                                    x-model="formData.contact_phone"
                                    placeholder="Contoh: 08123456789"
                                    class="form-input"
                                    required
                                >
                            </div>
                        </div>

                        <!-- Target Timeline -->
                        <div>
                            <label class="form-label">
                                Target Mulai Operasi
                            </label>
                            <select 
                                x-model="formData.target_timeline"
                                class="form-input form-select"
                            >
                                <option value="">Pilih target waktu...</option>
                                <option value="urgent">Segera (< 1 bulan)</option>
                                <option value="fast">Cepat (1-3 bulan)</option>
                                <option value="normal">Normal (3-6 bulan)</option>
                                <option value="planned">Terencana (6-12 bulan)</option>
                                <option value="flexible">Fleksibel (> 12 bulan)</option>
                            </select>
                            <p class="mt-1 text-xs" style="color: var(--text-secondary);">
                                <i class="fas fa-calendar-alt mr-1"></i>
                                Mempengaruhi strategi pengurusan dan biaya ekspres
                            </p>
                        </div>

                        <!-- Business Nature -->
                        <div>
                            <label class="form-label">
                                Sifat Usaha
                            </label>
                            <select 
                                x-model="formData.business_nature"
                                class="form-input form-select"
                            >
                                <option value="">Pilih sifat usaha...</option>
                                <option value="local_market">Pasar Lokal/Domestik</option>
                                <option value="export_oriented">Berorientasi Ekspor</option>
                                <option value="import_dependent">Bergantung Import</option>
                                <option value="b2b_services">Layanan B2B</option>
                                <option value="b2c_retail">Retail/B2C</option>
                                <option value="online_marketplace">Online/E-commerce</option>
                                <option value="franchise">Waralaba/Franchise</option>
                                <option value="government_contractor">Kontraktor Pemerintah</option>
                                <option value="high_risk">Berisiko Tinggi (Mining, Kimia, dll)</option>
                            </select>
                            <p class="mt-1 text-xs" style="color: var(--text-secondary);">
                                <i class="fas fa-cogs mr-1"></i>
                                Mempengaruhi jenis izin khusus yang diperlukan
                            </p>
                        </div>

                        <!-- Deliverables (Optional) -->
                        <div class="col-span-1 md:col-span-2 mt-6">
                            <label class="form-label">
                                Dokumen Spesifik yang Dibutuhkan (Opsional)
                            </label>
                            <textarea 
                                x-model="formData.deliverables"
                                rows="3"
                                placeholder="Contoh: NIB, Sertifikat Standar, Izin Lingkungan, AMDAL, Izin Impor, dll..."
                                class="form-input"
                            ></textarea>
                            <p class="mt-1 text-xs" style="color: var(--text-secondary);">
                                <i class="fas fa-robot mr-1"></i>
                                Kosongkan jika ingin rekomendasi lengkap dan otomatis dari AI
                            </p>
                        </div>
                    </div>

                    <!-- Quick Estimate Preview -->
                    <div 
                        x-show="quickEstimate && formData.business_size && formData.location_type" 
                        x-transition
                        class="p-8"
                        style="background: linear-gradient(135deg, rgba(22, 163, 74, 0.06) 0%, rgba(14, 165, 233, 0.06) 100%); border-top: 1px solid rgba(22, 163, 74, 0.2); border-bottom: 1px solid rgba(22, 163, 74, 0.2);"
                    >
                        <div class="flex items-center gap-3 mb-4">
                            <div class="flex items-center justify-center w-10 h-10 rounded-full" style="background: rgba(22, 163, 74, 0.15); color: var(--color-success);">
                                <i class="fas fa-calculator"></i>
                            </div>
                            <h3 class="text-xl font-bold" style="color: var(--text-primary);">
                                Estimasi Cepat (Preview)
                            </h3>
                        </div>

                        <div class="rounded-lg p-6" style="background: var(--surface); border: 1px solid rgba(22, 163, 74, 0.2);">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                                <div>
                                    <p class="text-sm mb-1" style="color: var(--text-secondary);">Subtotal</p>
                                    <p class="text-2xl font-bold" style="color: var(--text-primary);" x-text="quickEstimate?.estimate?.formatted?.subtotal || '-'"></p>
                                </div>
                                <div>
                                    <p class="text-sm mb-1" style="color: var(--text-secondary);">Total Estimasi</p>
                                    <p class="text-3xl font-bold" style="color: var(--color-success);" x-text="quickEstimate?.estimate?.formatted?.grand_total || '-'"></p>
                                </div>
                                <div>
                                    <p class="text-sm mb-1" style="color: var(--text-secondary);">Kisaran Biaya</p>
                                    <p class="text-lg font-semibold" style="color: var(--text-primary);" x-text="quickEstimate?.estimate?.formatted?.range || '-'"></p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 p-3 rounded-lg" style="background: rgba(14, 165, 233, 0.08);">
                                <i class="fas fa-info-circle" style="color: var(--color-accent);"></i>
                                <p class="text-sm" style="color: var(--text-secondary);">
                                    <strong>Note:</strong> Ini estimasi cepat. Submit form untuk analisis AI detail dengan breakdown lengkap.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div
                        x-show="quickEstimateError"
                        x-transition
                        class="mx-8 mb-4 px-4 py-3 rounded-lg border border-amber-200 bg-amber-50 text-amber-800 text-sm"
                    >
                        <div class="flex items-start gap-2">
                            <i class="fas fa-info-circle mt-0.5"></i>
                            <span x-text="quickEstimateError"></span>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="p-8" style="background: var(--surface-warm);">
                        <!-- Autosave Status Indicator -->
                        <div x-show="draftRestored || lastSaved" class="mb-4 flex items-center justify-between p-3 rounded-lg" style="background: rgba(22, 163, 74, 0.05); border: 1px solid rgba(22, 163, 74, 0.15);">
                            <div class="flex items-center gap-2 text-sm" style="color: var(--color-success);">
                                <i class="fas fa-save"></i>
                                <span x-text="draftRestored ? 'Draft tersimpan dipulihkan' : getLastSavedText()"></span>
                            </div>
                            <button
                                type="button"
                                @click="clearDraft(); draftRestored = false; Object.keys(formData).forEach(k => { if(typeof formData[k] === 'string') formData[k] = ''; else if(Array.isArray(formData[k])) formData[k] = []; }); selectedKBLI = null; quickEstimate = null;"
                                @mouseenter="$el.style.color='#dc2626'"
                                @mouseleave="$el.style.color='var(--text-tertiary)'"
                                class="text-xs px-2 py-1 rounded transition"
                                style="color: var(--text-tertiary); background: var(--surface);"
                            >
                                <i class="fas fa-times mr-1"></i>Hapus Draft
                            </button>
                        </div>
                        
                        <!-- Validation Errors Display -->
                        <div x-show="validationErrors.length > 0" class="validation-error mb-4">
                            <p class="validation-error-title"><i class="fas fa-exclamation-triangle mr-2"></i>Mohon lengkapi data berikut:</p>
                            <ul class="validation-error-list">
                                <template x-for="error in validationErrors" :key="error">
                                    <li x-text="error"></li>
                                </template>
                            </ul>
                        </div>

                        <button 
                            type="submit"
                            :disabled="submitting || !isFormValid()"
                            class="btn btn-primary btn-lg w-full flex items-center justify-center gap-3"
                        >
                            <span x-show="!submitting">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Dapatkan Estimasi Detail dengan AI
                            </span>
                            <span x-show="submitting" class="flex items-center gap-3">
                                <i class="fas fa-spinner fa-spin"></i>
                                <span>Processing AI Analysis...</span>
                                <span class="text-sm opacity-80">(~30 detik)</span>
                            </span>
                        </button>

                        <p class="text-center text-sm mt-4" style="color: var(--text-tertiary);">
                            <i class="fas fa-shield-alt mr-1"></i>
                            Data Anda aman dan terenkripsi. Kami tidak membagikan informasi Anda kepada pihak ketiga.
                        </p>
                    </div>

                </form>

            </div>

            <!-- Info Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                <div class="rounded-xl p-6" style="background: var(--surface); border: 1px solid var(--border-light);">
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-4" style="background: rgba(14, 165, 233, 0.1);">
                        <i class="fas fa-robot text-2xl" style="color: var(--color-accent);"></i>
                    </div>
                    <h3 class="font-bold mb-2" style="color: var(--text-primary);">AI-Powered Analysis</h3>
                    <p class="text-sm" style="color: var(--text-secondary);">
                        Estimasi biaya menggunakan AI dengan data perizinan terkini dan analisis kompleksitas dokumen.
                    </p>
                </div>

                <div class="rounded-xl p-6" style="background: var(--surface); border: 1px solid var(--border-light);">
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-4" style="background: rgba(249, 115, 22, 0.12);">
                        <i class="fas fa-file-invoice-dollar text-2xl" style="color: var(--color-secondary);"></i>
                    </div>
                    <h3 class="font-bold mb-2" style="color: var(--text-primary);">Breakdown Lengkap</h3>
                    <p class="text-sm" style="color: var(--text-secondary);">
                        Rincian biaya per dokumen, jadwal pengerjaan, dan estimasi waktu penyelesaian yang akurat.
                    </p>
                </div>

                <div class="rounded-xl p-6" style="background: var(--surface); border: 1px solid var(--border-light);">
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-4" style="background: rgba(22, 163, 74, 0.12);">
                        <i class="fas fa-headset text-2xl" style="color: var(--color-success);"></i>
                    </div>
                    <h3 class="font-bold mb-2" style="color: var(--text-primary);">Konsultasi Gratis</h3>
                    <p class="text-sm" style="color: var(--text-secondary);">
                        Tim ahli kami siap membantu diskusi lebih lanjut dan menjawab pertanyaan Anda via WhatsApp.
                    </p>
                </div>
            </div>

            <!-- Related Tools Section -->
            <div class="mt-12 pt-8" style="border-top: 1px solid var(--border-light);">
                <h3 class="text-xl font-bold mb-6 text-center" style="color: var(--text-primary);">
                    <i class="fas fa-tools mr-2" style="color: var(--color-accent);"></i>
                    Alat Digital Gratis Lainnya
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <a href="{{ route('polygon.shp.index') }}" class="group block rounded-xl p-6 hover:shadow-lg transition-all hover:scale-[1.02]" style="background: linear-gradient(135deg, rgba(22, 163, 74, 0.08) 0%, rgba(14, 165, 233, 0.08) 100%); border: 1px solid rgba(22, 163, 74, 0.2);">
                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition" style="background: linear-gradient(135deg, #16a34a 0%, #0ea5e9 100%);">
                                <i class="fas fa-draw-polygon text-2xl text-white"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <h4 class="font-bold" style="color: var(--text-primary);">Polygon SHP Maker</h4>
                                    <span class="px-2 py-0.5 text-xs font-bold text-white rounded-full" style="background: var(--color-success);">GRATIS</span>
                                </div>
                                <p class="text-sm mb-3" style="color: var(--text-secondary);">
                                    Buat file Shapefile (.shp) untuk upload OSS RBA. Gambar poligon di peta interaktif dengan proyeksi WGS84 standar.
                                </p>
                                <div class="inline-flex items-center gap-2 font-semibold text-sm group-hover:gap-3 transition-all" style="color: #15803d;">
                                    <span>Buat File SHP Sekarang</span>
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('services.index.id') }}" class="group block rounded-xl p-6 hover:shadow-lg transition-all hover:scale-[1.02]" style="background: linear-gradient(135deg, rgba(14, 165, 233, 0.08) 0%, rgba(249, 115, 22, 0.08) 100%); border: 1px solid rgba(14, 165, 233, 0.2);">
                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition" style="background: linear-gradient(135deg, var(--color-accent) 0%, var(--color-secondary) 100%);">
                                <i class="fas fa-briefcase text-2xl text-white"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold mb-1" style="color: var(--text-primary);">Layanan Perizinan</h4>
                                <p class="text-sm mb-3" style="color: var(--text-secondary);">
                                    Butuh bantuan profesional? Lihat daftar lengkap layanan pengurusan perizinan usaha dari tim ahli kami.
                                </p>
                                <div class="inline-flex items-center gap-2 font-semibold text-sm group-hover:gap-3 transition-all" style="color: var(--color-accent);">
                                    <span>Lihat Semua Layanan</span>
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Next Step Cross-Link Panel: AI estimator → with-team or other tools --}}
    <section class="section-sm" style="background: var(--bg-raised); border-top: 1px solid var(--border-subtle);">
        <div class="container-wide">
            <div class="text-center mb-8 max-w-2xl mx-auto">
                <span class="eyebrow" style="color: var(--text-muted);">Langkah Berikutnya</span>
                <h2 class="display-md mt-2 mb-3">Setelah dapat estimasi, lanjutkan ke…</h2>
                <p style="color: var(--text-secondary);">Gunakan tools lain atau langsung didampingi tim ahli kami.</p>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 max-w-5xl mx-auto">
                <a href="{{ route('calculator.index') }}" class="premium-card hover:no-underline" style="border-color: rgba(var(--tools-rgb),.2); background: var(--tools-glow);">
                    <div class="editorial-icon-badge is-tools is-circle mb-3" style="width:2.5rem;height:2.5rem;">
                        <i class="fas fa-calculator" aria-hidden="true"></i>
                    </div>
                    <div class="text-[10px] font-bold uppercase tracking-[.14em] mb-2" style="color: var(--tools);">Tools · Gratis</div>
                    <h3 class="font-bold text-base mb-1" style="color: var(--text-primary);">Kalkulator Lanjutan</h3>
                    <p class="text-sm" style="color: var(--text-secondary);">Hitung biaya per item dengan parameter detail.</p>
                </a>
                <a href="{{ route('polygon.shp.index') }}" class="premium-card hover:no-underline" style="border-color: rgba(var(--tools-rgb),.2); background: var(--tools-glow);">
                    <div class="editorial-icon-badge is-tools is-circle mb-3" style="width:2.5rem;height:2.5rem;">
                        <i class="fas fa-draw-polygon" aria-hidden="true"></i>
                    </div>
                    <div class="text-[10px] font-bold uppercase tracking-[.14em] mb-2" style="color: var(--tools);">Tools · Gratis</div>
                    <h3 class="font-bold text-base mb-1" style="color: var(--text-primary);">SHP Polygon Maker</h3>
                    <p class="text-sm" style="color: var(--text-secondary);">Buat shapefile lokasi untuk dokumen perizinan.</p>
                </a>
                <a href="{{ route('services.index.id') }}" class="premium-card hover:no-underline" style="border-color: rgba(var(--accent-rgb),.2); background: var(--accent-glow);">
                    <div class="editorial-icon-badge is-circle mb-3" style="width:2.5rem;height:2.5rem;">
                        <i class="fas fa-handshake" aria-hidden="true"></i>
                    </div>
                    <div class="text-[10px] font-bold uppercase tracking-[.14em] mb-2" style="color: var(--accent-text);">Didampingi tim</div>
                    <h3 class="font-bold text-base mb-1" style="color: var(--text-primary);">Lihat Layanan</h3>
                    <p class="text-sm" style="color: var(--text-secondary);">Tim ahli mendampingi proses end-to-end.</p>
                </a>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
// KBLI Autocomplete Component
function kbliAutocomplete() {
    return {
        search: '',
        results: [],
        selectedKBLI: null,
        showResults: false,
        loading: false,
        error: '',
        highlightedIndex: -1,

        async searchKBLI() {
            if (this.search.length < 2) {
                this.results = [];
                this.showResults = false;
                return;
            }

            this.loading = true;
            this.error = '';

            try {
                const response = await fetch(`/api/kbli/search?q=${encodeURIComponent(this.search)}&limit=10`);
                const data = await response.json();

                if (data.success) {
                    this.results = data.data;
                    this.showResults = true;
                    this.highlightedIndex = -1;
                } else {
                    this.error = data.message || 'Gagal mencari KBLI';
                }
            } catch (error) {
                console.error('KBLI search error:', error);
                this.error = 'Terjadi kesalahan saat mencari KBLI';
            } finally {
                this.loading = false;
            }
        },

        selectKBLI(kbli) {
            this.selectedKBLI = kbli;
            this.search = kbli.description;
            this.showResults = false;
            this.results = [];
            
            // Trigger form update
            const event = new CustomEvent('kbli-selected', { detail: kbli });
            window.dispatchEvent(event);
        },

        clearSelection() {
            this.selectedKBLI = null;
            this.search = '';
            this.results = [];
            this.showResults = false;
            
            const event = new CustomEvent('kbli-cleared');
            window.dispatchEvent(event);
        },

        navigateDown() {
            if (this.highlightedIndex < this.results.length - 1) {
                this.highlightedIndex++;
            }
        },

        navigateUp() {
            if (this.highlightedIndex > 0) {
                this.highlightedIndex--;
            }
        },

        selectHighlighted() {
            if (this.highlightedIndex >= 0 && this.results[this.highlightedIndex]) {
                this.selectKBLI(this.results[this.highlightedIndex]);
            }
        }
    };
}

// Main Consultation Form Component
function consultationForm() {
    const AUTOSAVE_KEY = 'bizmark_consultation_draft';
    
    return {
        formData: {
            kbli_code: '',
            additional_activities: [],
            business_size: '',
            location: '',
            location_type: '',
            geographic_region: '',
            entity_type: '',
            investment_level: '',
            employee_count: '',
            target_timeline: '',
            business_nature: '',
            applicant_name: '',
            applicant_email: '',
            contact_phone: '',
            deliverables: ''
        },
        selectedKBLI: null,
        quickEstimate: null,
        quickEstimateError: '',
        quickEstimateRequestController: null,
        submitting: false,
        validationErrors: [],
        draftRestored: false,
        lastSaved: null,

        init() {
            // Restore saved draft from localStorage
            this.restoreDraft();
            
            // Setup autosave on form changes
            this.$watch('formData', () => {
                this.saveDraft();
            }, { deep: true });
            
            // Listen for KBLI selection
            window.addEventListener('kbli-selected', (event) => {
                this.selectedKBLI = event.detail;
                this.formData.kbli_code = event.detail.code;
                this.saveDraft();
                
                // Auto-calculate if business info is filled
                if (this.formData.business_size && this.formData.location_type) {
                    this.calculateQuickEstimate();
                }
            });

            window.addEventListener('kbli-cleared', () => {
                this.selectedKBLI = null;
                this.formData.kbli_code = '';
                this.quickEstimate = null;
                this.saveDraft();
            });
        },
        
        // Autosave: Save draft to localStorage
        saveDraft() {
            try {
                const draft = {
                    formData: this.formData,
                    selectedKBLI: this.selectedKBLI,
                    savedAt: new Date().toISOString()
                };
                localStorage.setItem(AUTOSAVE_KEY, JSON.stringify(draft));
                this.lastSaved = new Date();
            } catch (e) {
                console.warn('Could not save draft:', e);
            }
        },
        
        // Autosave: Restore draft from localStorage
        restoreDraft() {
            try {
                const saved = localStorage.getItem(AUTOSAVE_KEY);
                if (saved) {
                    const draft = JSON.parse(saved);
                    // Only restore if draft is less than 24 hours old
                    const savedAt = new Date(draft.savedAt);
                    const hoursSinceSave = (Date.now() - savedAt.getTime()) / (1000 * 60 * 60);
                    
                    if (hoursSinceSave < 24) {
                        this.formData = { ...this.formData, ...draft.formData };
                        if (draft.selectedKBLI) {
                            this.selectedKBLI = draft.selectedKBLI;
                            // Trigger KBLI display update
                            setTimeout(() => {
                                window.dispatchEvent(new CustomEvent('kbli-selected', { 
                                    detail: draft.selectedKBLI 
                                }));
                            }, 100);
                        }
                        this.draftRestored = true;
                        this.lastSaved = savedAt;
                    }
                }
            } catch (e) {
                console.warn('Could not restore draft:', e);
            }
        },
        
        // Clear saved draft
        clearDraft() {
            try {
                localStorage.removeItem(AUTOSAVE_KEY);
                this.draftRestored = false;
                this.lastSaved = null;
            } catch (e) {
                console.warn('Could not clear draft:', e);
            }
        },
        
        // Format last saved time
        getLastSavedText() {
            if (!this.lastSaved) return '';
            const now = new Date();
            const diff = Math.floor((now - this.lastSaved) / 1000);
            if (diff < 60) return 'Baru saja disimpan';
            if (diff < 3600) return `Disimpan ${Math.floor(diff / 60)} menit lalu`;
            return `Disimpan ${Math.floor(diff / 3600)} jam lalu`;
        },

        createEmptyActivity() {
            return {
                kbli_code: '',
                description: ''
            };
        },

        addAdditionalActivity() {
            if (this.formData.additional_activities.length >= 3) {
                return;
            }

            this.formData.additional_activities.push(this.createEmptyActivity());
        },

        removeAdditionalActivity(index) {
            this.formData.additional_activities.splice(index, 1);
        },

        buildSubmitPayload() {
            return {
                ...this.formData,
                additional_activities: (this.formData.additional_activities || [])
                    .map((activity) => ({
                        kbli_code: (activity.kbli_code || '').trim(),
                        description: (activity.description || '').trim(),
                    }))
                    .filter((activity) => activity.kbli_code || activity.description),
            };
        },

        async calculateQuickEstimate() {
            if (!this.formData.kbli_code || !this.formData.business_size || !this.formData.location_type) {
                this.quickEstimateError = '';
                return;
            }

            if (this.quickEstimateRequestController) {
                this.quickEstimateRequestController.abort();
            }

            this.quickEstimateRequestController = new AbortController();
            this.quickEstimateError = '';

            try {
                const response = await fetch('/api/consultation/quick-estimate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    signal: this.quickEstimateRequestController.signal,
                    body: JSON.stringify({
                        kbli_code: this.formData.kbli_code,
                        business_size: this.formData.business_size,
                        location_type: this.formData.location_type
                    })
                });

                const data = await this.parseResponseSafely(response);

                if (!response.ok) {
                    if (response.status === 429) {
                        const retryAfter = response.headers.get('Retry-After');
                        this.quickEstimateError = retryAfter
                            ? `Terlalu banyak percobaan estimasi cepat. Coba lagi dalam ${retryAfter} detik.`
                            : 'Terlalu banyak percobaan estimasi cepat. Mohon tunggu sebentar.';
                    } else {
                        this.quickEstimateError = data?.message || 'Gagal menghitung estimasi cepat.';
                    }
                    return;
                }

                if (data.success) {
                    this.quickEstimate = data.data;
                    this.quickEstimateError = '';
                } else {
                    this.quickEstimateError = data?.message || 'Gagal menghitung estimasi cepat.';
                }
            } catch (error) {
                if (error.name === 'AbortError') {
                    return;
                }
                console.error('Quick estimate error:', error);
                this.quickEstimateError = 'Terjadi gangguan jaringan saat menghitung estimasi cepat.';
            } finally {
                this.quickEstimateRequestController = null;
            }
        },

        async parseResponseSafely(response) {
            const contentType = response.headers.get('content-type') || '';
            if (!contentType.includes('application/json')) {
                return {
                    success: false,
                    message: response.status >= 500
                        ? 'Server sedang mengalami gangguan. Silakan coba lagi.'
                        : 'Respons server tidak valid. Silakan coba ulangi.'
                };
            }

            try {
                return await response.json();
            } catch (e) {
                return {
                    success: false,
                    message: 'Gagal memproses respons server. Silakan coba lagi.'
                };
            }
        },

        isFormValid() {
            return this.selectedKBLI &&
                   this.formData.business_size &&
                   this.formData.location_type &&
                   this.formData.location &&
                   this.formData.geographic_region &&
                   this.formData.entity_type &&
                   this.formData.investment_level &&
                   this.formData.applicant_name &&
                   this.formData.contact_phone;
        },

        validateForm() {
            this.validationErrors = [];
            
            if (!this.formData.kbli_code) {
                this.validationErrors.push('Pilih jenis usaha (KBLI)');
            }
            if (!this.formData.business_size) {
                this.validationErrors.push('Pilih skala bisnis');
            }
            if (!this.formData.location_type) {
                this.validationErrors.push('Pilih zona/kawasan');
            }
            if (!this.formData.location || this.formData.location.trim() === '') {
                this.validationErrors.push('Isi kota/kabupaten');
            }
            if (!this.formData.geographic_region) {
                this.validationErrors.push('Pilih wilayah geografis');
            }
            if (!this.formData.entity_type) {
                this.validationErrors.push('Pilih jenis badan usaha');
            }
            if (!this.formData.investment_level) {
                this.validationErrors.push('Pilih level investasi');
            }
            if (!this.formData.applicant_name || this.formData.applicant_name.trim() === '') {
                this.validationErrors.push('Isi nama pengusul/entitas');
            }
            if (!this.formData.contact_phone || this.formData.contact_phone.trim() === '') {
                this.validationErrors.push('Isi nomor WhatsApp');
            }
            
            return this.validationErrors.length === 0;
        },

        async submitForm() {
            // Run validation first
            if (!this.validateForm()) {
                // Scroll to errors
                document.querySelector('.validation-error')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }

            this.submitting = true;

            try {
                const response = await fetch('/api/consultation/submit', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.buildSubmitPayload())
                });

                const data = await this.parseResponseSafely(response);

                if (!response.ok) {
                    if (response.status === 429) {
                        const retryAfter = response.headers.get('Retry-After');
                        this.validationErrors = [retryAfter
                            ? `Terlalu banyak percobaan. Silakan coba lagi dalam ${retryAfter} detik.`
                            : 'Terlalu banyak percobaan. Mohon tunggu sebentar lalu coba lagi.'];
                    } else if (response.status === 422 && data?.errors) {
                        this.validationErrors = [];
                        for (const messages of Object.values(data.errors)) {
                            this.validationErrors.push(...messages);
                        }
                    } else {
                        this.validationErrors = [data?.message || 'Terjadi kesalahan. Silakan coba lagi.'];
                    }
                    return;
                }

                if (data.success) {
                    // Clear saved draft on successful submission
                    this.clearDraft();
                    // Redirect to result page
                    window.location.href = `/estimasi-biaya/hasil/${data.data.request_id}`;
                } else {
                    // Handle validation errors from API
                    if (data.errors) {
                        this.validationErrors = [];
                        for (const [field, messages] of Object.entries(data.errors)) {
                            this.validationErrors.push(...messages);
                        }
                    } else {
                        this.validationErrors = [data.message || 'Terjadi kesalahan. Silakan coba lagi.'];
                    }
                }
            } catch (error) {
                console.error('Submit error:', error);
                this.validationErrors = ['Terjadi kesalahan jaringan. Silakan coba lagi.'];
            } finally {
                this.submitting = false;
            }
        }
    };
}
</script>
@endpush
@endsection
