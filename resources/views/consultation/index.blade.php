@extends('layouts.app')

@section('meta_title', 'Estimasi Biaya Perizinan - Bizmark.ID')
@section('meta_description', 'Dapatkan estimasi biaya perizinan usaha Anda dengan AI analysis. Pilih jenis usaha (KBLI), isi informasi bisnis, dan terima estimasi biaya instan dengan rincian lengkap.')
@section('meta_keywords', 'estimasi biaya perizinan, kalkulator biaya izin, biaya pengurusan izin, konsultasi perizinan online, KBLI search')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
    <!-- Header Section -->
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">
                        Estimasi Biaya Perizinan
                    </span>
                </h1>
                <p class="text-lg text-gray-600 dark:text-gray-300 mb-6">
                    Dapatkan estimasi biaya perizinan usaha Anda dengan AI analysis.<br>
                    Proses cepat, transparan, dan akurat berdasarkan jenis usaha dan kompleksitas perizinan.
                </p>
                <div class="flex items-center justify-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-robot text-blue-500"></i>
                        <span>AI-Powered</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-bolt text-yellow-500"></i>
                        <span>Instant Result</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-lock text-green-500"></i>
                        <span>Data Aman</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Section -->
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                
                <form id="consultation-form" x-data="consultationForm()" @submit.prevent="submitForm">
                    
                    <!-- Step 1: KBLI Selection -->
                    <div class="p-8 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="flex items-center justify-center w-10 h-10 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 rounded-full font-bold">
                                1
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                                Pilih Jenis Usaha (KBLI)
                            </h2>
                        </div>

                        <!-- KBLI Autocomplete Component -->
                        <div class="relative" x-data="kbliAutocomplete()" @click.away="showResults = false">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Cari KBLI <span class="text-red-500">*</span>
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
                                    class="w-full px-4 py-3 pr-10 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                    :class="{ 'border-red-500': error }"
                                >
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fas fa-search text-gray-400" x-show="!loading"></i>
                                    <i class="fas fa-spinner fa-spin text-blue-500" x-show="loading"></i>
                                </div>
                            </div>

                            <!-- Selected KBLI Display -->
                            <div x-show="selectedKBLI" class="mt-3 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="font-mono font-bold text-blue-600 dark:text-blue-400" x-text="selectedKBLI?.code"></span>
                                            <span class="px-2 py-0.5 text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded" x-text="selectedKBLI?.complexity_level"></span>
                                        </div>
                                        <p class="text-sm text-gray-900 dark:text-white font-medium" x-text="selectedKBLI?.description"></p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1" x-text="'Kategori: ' + selectedKBLI?.category"></p>
                                    </div>
                                    <button 
                                        type="button"
                                        @click="clearSelection"
                                        class="ml-3 text-gray-400 hover:text-red-500 transition"
                                    >
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Autocomplete Dropdown -->
                            <div 
                                x-show="showResults && results.length > 0"
                                x-transition
                                class="absolute z-50 w-full mt-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-xl max-h-80 overflow-y-auto"
                            >
                                <template x-for="(kbli, index) in results" :key="kbli.code">
                                    <button
                                        type="button"
                                        @click="selectKBLI(kbli)"
                                        class="w-full px-4 py-3 text-left hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700 last:border-b-0 transition"
                                        :class="{ 'bg-blue-50 dark:bg-blue-900/20': highlightedIndex === index }"
                                    >
                                        <div class="flex items-start gap-3">
                                            <span class="font-mono text-sm font-bold text-blue-600 dark:text-blue-400 mt-0.5" x-text="kbli.code"></span>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate" x-text="kbli.description"></p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" x-text="kbli.category"></p>
                                            </div>
                                            <span 
                                                class="px-2 py-0.5 text-xs font-medium rounded shrink-0"
                                                :class="{
                                                    'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300': kbli.complexity_level === 'low',
                                                    'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300': kbli.complexity_level === 'medium',
                                                    'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300': kbli.complexity_level === 'high'
                                                }"
                                                x-text="kbli.complexity_level"
                                            ></span>
                                        </div>
                                    </button>
                                </template>
                            </div>

                            <!-- Error Message -->
                            <p x-show="error" class="mt-2 text-sm text-red-600 dark:text-red-400" x-text="error"></p>
                            
                            <!-- No Results Message -->
                            <p x-show="!loading && search.length >= 2 && results.length === 0 && !selectedKBLI" class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                <i class="fas fa-info-circle mr-1"></i>
                                Tidak ada hasil. Coba kata kunci lain.
                            </p>

                            <!-- Popular KBLI Suggestions -->
                            <div x-show="!selectedKBLI && search.length === 0" class="mt-4">
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-fire text-orange-500 mr-1"></i>
                                    KBLI Populer:
                                </p>
                                <div class="flex flex-wrap gap-2">
                                    <button 
                                        type="button"
                                        @click="search = 'restoran'; searchKBLI()"
                                        class="px-3 py-1.5 text-sm bg-gray-100 dark:bg-gray-700 hover:bg-blue-100 dark:hover:bg-blue-900 text-gray-700 dark:text-gray-300 rounded-lg transition"
                                    >
                                        Restoran
                                    </button>
                                    <button 
                                        type="button"
                                        @click="search = 'toko'; searchKBLI()"
                                        class="px-3 py-1.5 text-sm bg-gray-100 dark:bg-gray-700 hover:bg-blue-100 dark:hover:bg-blue-900 text-gray-700 dark:text-gray-300 rounded-lg transition"
                                    >
                                        Toko
                                    </button>
                                    <button 
                                        type="button"
                                        @click="search = 'konstruksi'; searchKBLI()"
                                        class="px-3 py-1.5 text-sm bg-gray-100 dark:bg-gray-700 hover:bg-blue-100 dark:hover:bg-blue-900 text-gray-700 dark:text-gray-300 rounded-lg transition"
                                    >
                                        Konstruksi
                                    </button>
                                    <button 
                                        type="button"
                                        @click="search = 'manufaktur'; searchKBLI()"
                                        class="px-3 py-1.5 text-sm bg-gray-100 dark:bg-gray-700 hover:bg-blue-100 dark:hover:bg-blue-900 text-gray-700 dark:text-gray-300 rounded-lg transition"
                                    >
                                        Manufaktur
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Business Information -->
                    <div class="p-8 border-b border-gray-200 dark:border-gray-700" x-show="selectedKBLI">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="flex items-center justify-center w-10 h-10 bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-300 rounded-full font-bold">
                                2
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                                Informasi Bisnis
                            </h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Business Size -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Skala Usaha <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    x-model="formData.business_size"
                                    @change="calculateQuickEstimate"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                    required
                                >
                                    <option value="">Pilih skala usaha...</option>
                                    <option value="micro">Mikro (Omzet < 300 juta/tahun)</option>
                                    <option value="small">Kecil (Omzet 300 juta - 2.5 miliar)</option>
                                    <option value="medium">Menengah (Omzet 2.5 - 50 miliar)</option>
                                    <option value="large">Besar (Omzet > 50 miliar)</option>
                                </select>
                            </div>

                            <!-- Location Type -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Tipe Lokasi <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    x-model="formData.location_type"
                                    @change="calculateQuickEstimate"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                    required
                                >
                                    <option value="">Pilih tipe lokasi...</option>
                                    <option value="jakarta">DKI Jakarta</option>
                                    <option value="jabodetabek">Jabodetabek (di luar Jakarta)</option>
                                    <option value="jawa_bali">Jawa & Bali</option>
                                    <option value="luar_jawa">Luar Jawa</option>
                                </select>
                            </div>

                            <!-- Location -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Lokasi Usaha <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    x-model="formData.location"
                                    placeholder="Contoh: Jakarta Selatan, Tangerang, Bandung..."
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                    required
                                >
                            </div>

                            <!-- Investment Level -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Nilai Investasi <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    x-model="formData.investment_level"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                    required
                                >
                                    <option value="">Pilih nilai investasi...</option>
                                    <option value="under_1b">< 1 Miliar</option>
                                    <option value="1b_5b">1 - 5 Miliar</option>
                                    <option value="5b_10b">5 - 10 Miliar</option>
                                    <option value="above_10b">> 10 Miliar</option>
                                </select>
                            </div>

                            <!-- Employee Count -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Jumlah Karyawan
                                </label>
                                <input 
                                    type="number" 
                                    x-model="formData.employee_count"
                                    placeholder="Perkiraan jumlah karyawan..."
                                    min="1"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                >
                            </div>

                            <!-- Contact Info -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Nomor WhatsApp <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="tel" 
                                    x-model="formData.contact_phone"
                                    placeholder="Contoh: 08123456789"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                    required
                                >
                            </div>
                        </div>

                        <!-- Deliverables (Optional) -->
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Dokumen yang Dibutuhkan (Opsional)
                            </label>
                            <textarea 
                                x-model="formData.deliverables"
                                rows="3"
                                placeholder="Contoh: NIB, Sertifikat Standar, Izin Lingkungan, dll..."
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                            ></textarea>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Kosongkan jika ingin rekomendasi otomatis dari AI
                            </p>
                        </div>
                    </div>

                    <!-- Quick Estimate Preview -->
                    <div 
                        x-show="quickEstimate && formData.business_size && formData.location_type" 
                        x-transition
                        class="p-8 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border-y border-green-200 dark:border-green-800"
                    >
                        <div class="flex items-center gap-3 mb-4">
                            <div class="flex items-center justify-center w-10 h-10 bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300 rounded-full">
                                <i class="fas fa-calculator"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                                Estimasi Cepat (Preview)
                            </h3>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-green-200 dark:border-green-700">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Subtotal</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="quickEstimate?.estimate?.formatted?.subtotal || '-'"></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Estimasi</p>
                                    <p class="text-3xl font-bold text-green-600 dark:text-green-400" x-text="quickEstimate?.estimate?.formatted?.grand_total || '-'"></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Kisaran Biaya</p>
                                    <p class="text-lg font-semibold text-gray-700 dark:text-gray-300" x-text="quickEstimate?.estimate?.formatted?.range || '-'"></p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                <i class="fas fa-info-circle text-blue-500"></i>
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    <strong>Note:</strong> Ini estimasi cepat. Submit form untuk analisis AI detail dengan breakdown lengkap.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="p-8 bg-gray-50 dark:bg-gray-900/50">
                        <button 
                            type="submit"
                            :disabled="submitting || !selectedKBLI || !formData.business_size || !formData.location_type"
                            class="w-full px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:shadow-lg flex items-center justify-center gap-3"
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

                        <p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-4">
                            <i class="fas fa-shield-alt mr-1"></i>
                            Data Anda aman dan terenkripsi. Kami tidak membagikan informasi Anda kepada pihak ketiga.
                        </p>
                    </div>

                </form>

            </div>

            <!-- Info Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-robot text-2xl text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 dark:text-white mb-2">AI-Powered Analysis</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Estimasi biaya menggunakan AI dengan data perizinan terkini dan analisis kompleksitas dokumen.
                    </p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-file-invoice-dollar text-2xl text-purple-600 dark:text-purple-400"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 dark:text-white mb-2">Breakdown Lengkap</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Rincian biaya per dokumen, timeline pengerjaan, dan estimasi waktu penyelesaian yang akurat.
                    </p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-headset text-2xl text-green-600 dark:text-green-400"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 dark:text-white mb-2">Konsultasi Gratis</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Tim ahli kami siap membantu diskusi lebih lanjut dan menjawab pertanyaan Anda via WhatsApp.
                    </p>
                </div>
            </div>
        </div>
    </div>
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
    return {
        formData: {
            kbli_code: '',
            business_size: '',
            location: '',
            location_type: '',
            investment_level: '',
            employee_count: '',
            contact_phone: '',
            deliverables: ''
        },
        selectedKBLI: null,
        quickEstimate: null,
        submitting: false,

        init() {
            // Listen for KBLI selection
            window.addEventListener('kbli-selected', (event) => {
                this.selectedKBLI = event.detail;
                this.formData.kbli_code = event.detail.code;
                
                // Auto-calculate if business info is filled
                if (this.formData.business_size && this.formData.location_type) {
                    this.calculateQuickEstimate();
                }
            });

            window.addEventListener('kbli-cleared', () => {
                this.selectedKBLI = null;
                this.formData.kbli_code = '';
                this.quickEstimate = null;
            });
        },

        async calculateQuickEstimate() {
            if (!this.formData.kbli_code || !this.formData.business_size || !this.formData.location_type) {
                return;
            }

            try {
                const response = await fetch('/api/consultation/quick-estimate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        kbli_code: this.formData.kbli_code,
                        business_size: this.formData.business_size,
                        location_type: this.formData.location_type
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.quickEstimate = data.data;
                }
            } catch (error) {
                console.error('Quick estimate error:', error);
            }
        },

        async submitForm() {
            if (!this.formData.kbli_code) {
                alert('Silakan pilih jenis usaha (KBLI) terlebih dahulu');
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
                    body: JSON.stringify(this.formData)
                });

                const data = await response.json();

                if (data.success) {
                    // Redirect to result page or show success modal
                    window.location.href = `/estimasi-biaya/hasil/${data.data.request_id}`;
                } else {
                    alert(data.message || 'Terjadi kesalahan. Silakan coba lagi.');
                }
            } catch (error) {
                console.error('Submit error:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
            } finally {
                this.submitting = false;
            }
        }
    };
}
</script>
@endpush
@endsection
