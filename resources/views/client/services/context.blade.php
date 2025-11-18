@extends('client.layouts.app')

@section('title', 'Konteks Bisnis - BizMark')

@section('content')
<div class="space-y-1" x-data="contextForm()">
    <!-- Hero: Selected KBLI Info -->
    <div class="bg-[#0a66c2] text-white border-y border-[#0a66c2]">
        <div class="px-4 lg:px-6 py-6">
            <!-- Back Button -->
            <a href="{{ route('client.services.index') }}" class="inline-flex items-center text-white/90 hover:text-white transition-colors mb-4">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Katalog
            </a>

            <!-- KBLI Info -->
            <div class="flex items-start gap-4 mb-6">
                <div class="flex-shrink-0">
                    <div class="w-16 h-16 bg-white/10 backdrop-blur-sm flex items-center justify-center">
                        <i class="fas fa-briefcase text-2xl text-white"></i>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-3 mb-2">
                        <span class="px-3 py-1 bg-white/10 backdrop-blur-sm text-white text-xs font-mono font-semibold">
                            {{ $kbli->code }}
                        </span>
                        <span class="text-sm text-white/80">
                            Sektor {{ $kbli->sector }}
                        </span>
                    </div>
                    <h1 class="text-2xl lg:text-3xl font-bold mb-2">
                        {{ $kbli->description }}
                    </h1>
                    <p class="text-white/80 text-sm">
                        Lengkapi informasi bisnis Anda untuk mendapatkan rekomendasi perizinan yang akurat
                    </p>
                </div>
            </div>

            <!-- Stats: Context Benefits -->
            <div class="grid grid-cols-3 gap-3">
                <div class="bg-white/10 backdrop-blur-sm px-4 py-3">
                    <div class="text-2xl font-bold mb-1">4</div>
                    <div class="text-xs text-white/80">Langkah Mudah</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm px-4 py-3">
                    <div class="text-2xl font-bold mb-1">100%</div>
                    <div class="text-xs text-white/80">Gratis Analisis</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm px-4 py-3">
                    <div class="text-2xl font-bold mb-1">5</div>
                    <div class="text-xs text-white/80">Menit Isi Form</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Indicator -->
    <div class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700">
        <div class="px-4 lg:px-6 py-4">
            <div class="flex items-center justify-center gap-2 mb-3 max-w-md mx-auto">
                <template x-for="(step, index) in steps" :key="index">
                    <div class="flex items-center">
                        <div 
                            class="w-10 h-10 flex items-center justify-center font-semibold transition-all"
                            :class="{
                                'bg-[#0a66c2] text-white': index <= currentStep,
                                'bg-gray-200 dark:bg-gray-700 text-gray-500': index > currentStep
                            }"
                        >
                            <span x-text="index + 1"></span>
                        </div>
                        <div class="w-12 h-1 mx-1" x-show="index < steps.length - 1"
                             :class="index < currentStep ? 'bg-[#0a66c2]' : 'bg-gray-200 dark:bg-gray-700'"></div>
                    </div>
                </template>
            </div>
            <div class="text-center">
                <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="steps[currentStep]"></p>
            </div>
        </div>
    </div>

    <!-- Context Form -->
    <form 
        action="{{ route('client.services.context', $kbli->code) }}" 
        method="POST" 
        @submit.prevent="submitForm"
        class="space-y-1"
    >
        @csrf

        <!-- Step 1: Business Scale -->
        <div x-show="currentStep === 0" x-transition class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700">
            <div class="px-4 lg:px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Skala Usaha
                </h2>
            </div>

            <div class="px-4 lg:px-6 py-4 space-y-4">
                <div class="grid md:grid-cols-2 gap-4">
                    <label class="flex items-center p-4 border-2 cursor-pointer transition-all active:scale-95"
                           :class="formData.business_scale === 'mikro' ? 'border-[#0a66c2] bg-[#0a66c2]/5' : 'border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700'">
                        <input type="radio" name="business_scale" value="mikro" x-model="formData.business_scale" class="mr-3 text-[#0a66c2]">
                        <div>
                            <div class="font-medium text-gray-900 dark:text-white">Usaha Mikro</div>
                            <div class="text-xs text-gray-500">Aset ≤ Rp 50 juta atau Omzet ≤ Rp 300 juta/tahun</div>
                        </div>
                    </label>

                    <label class="flex items-center p-4 border-2 cursor-pointer transition-all active:scale-95"
                           :class="formData.business_scale === 'kecil' ? 'border-[#0a66c2] bg-[#0a66c2]/5' : 'border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700'">
                        <input type="radio" name="business_scale" value="kecil" x-model="formData.business_scale" class="mr-3 text-[#0a66c2]">
                        <div>
                            <div class="font-medium text-gray-900 dark:text-white">Usaha Kecil</div>
                            <div class="text-xs text-gray-500">Aset Rp 50 juta - Rp 500 juta</div>
                        </div>
                    </label>

                    <label class="flex items-center p-4 border-2 cursor-pointer transition-all active:scale-95"
                           :class="formData.business_scale === 'menengah' ? 'border-[#0a66c2] bg-[#0a66c2]/5' : 'border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700'">
                        <input type="radio" name="business_scale" value="menengah" x-model="formData.business_scale" class="mr-3 text-[#0a66c2]">
                        <div>
                            <div class="font-medium text-gray-900 dark:text-white">Usaha Menengah</div>
                            <div class="text-xs text-gray-500">Aset Rp 500 juta - Rp 10 miliar</div>
                        </div>
                    </label>

                    <label class="flex items-center p-4 border-2 cursor-pointer transition-all active:scale-95"
                           :class="formData.business_scale === 'besar' ? 'border-[#0a66c2] bg-[#0a66c2]/5' : 'border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700'">
                        <input type="radio" name="business_scale" value="besar" x-model="formData.business_scale" class="mr-3 text-[#0a66c2]">
                        <div>
                            <div class="font-medium text-gray-900 dark:text-white">Usaha Besar</div>
                            <div class="text-xs text-gray-500">Aset > Rp 10 miliar</div>
                        </div>
                    </label>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Luas Tanah (m²) <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            name="land_area" 
                            x-model="formData.land_area"
                            step="0.01"
                            min="0"
                            required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-transparent"
                            placeholder="Contoh: 500"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Luas Bangunan (m²)
                        </label>
                        <input 
                            type="number" 
                            name="building_area" 
                            x-model="formData.building_area"
                            step="0.01"
                            min="0"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-transparent"
                            placeholder="Contoh: 300"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Jumlah Lantai
                        </label>
                        <input 
                            type="number" 
                            name="number_of_floors" 
                            x-model="formData.number_of_floors"
                            min="1"
                            max="100"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-transparent"
                            placeholder="Contoh: 2"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nilai Investasi (Rp)
                        </label>
                        <input 
                            type="number" 
                            name="investment_value" 
                            x-model="formData.investment_value"
                            step="1000000"
                            min="0"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-transparent"
                            placeholder="Contoh: 5000000000"
                        >
                        <p class="text-xs text-gray-500 mt-1" x-show="formData.investment_value > 0">
                            <span x-text="formatCurrency(formData.investment_value)"></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Location Details -->
        <div x-show="currentStep === 1" x-transition class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700">
            <div class="px-4 lg:px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Lokasi Proyek
                </h2>
            </div>

            <div class="px-4 lg:px-6 py-4 space-y-4">
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Provinsi <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="province" 
                            x-model="formData.province"
                            required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-transparent"
                            placeholder="Contoh: DKI Jakarta"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Kota/Kabupaten <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="city" 
                            x-model="formData.city"
                            required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-transparent"
                            placeholder="Contoh: Jakarta Selatan"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Kecamatan
                        </label>
                        <input 
                            type="text" 
                            name="district" 
                            x-model="formData.district"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-transparent"
                            placeholder="Contoh: Kebayoran Baru"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Jenis Zona
                        </label>
                        <select 
                            name="zone_type" 
                            x-model="formData.zone_type"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-transparent"
                        >
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
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Kategori Lokasi <span class="text-red-500">*</span>
                    </label>
                    <div class="grid md:grid-cols-3 gap-3">
                        <label class="flex items-center p-4 border-2 cursor-pointer transition-all active:scale-95"
                               :class="formData.location_category === 'perkotaan' ? 'border-[#0a66c2] bg-[#0a66c2]/5' : 'border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700'">
                            <input type="radio" name="location_category" value="perkotaan" x-model="formData.location_category" required class="mr-3 text-[#0a66c2]">
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white text-sm">Perkotaan</div>
                                <div class="text-xs text-gray-500">Area komersial, pusat kota</div>
                            </div>
                        </label>

                        <label class="flex items-center p-4 border-2 cursor-pointer transition-all active:scale-95"
                               :class="formData.location_category === 'pedesaan' ? 'border-[#0a66c2] bg-[#0a66c2]/5' : 'border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700'">
                            <input type="radio" name="location_category" value="pedesaan" x-model="formData.location_category" required class="mr-3 text-[#0a66c2]">
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white text-sm">Pedesaan</div>
                                <div class="text-xs text-gray-500">Area pertanian, desa</div>
                            </div>
                        </label>

                        <label class="flex items-center p-4 border-2 cursor-pointer transition-all active:scale-95"
                               :class="formData.location_category === 'kawasan_industri' ? 'border-[#0a66c2] bg-[#0a66c2]/5' : 'border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700'">
                            <input type="radio" name="location_category" value="kawasan_industri" x-model="formData.location_category" required class="mr-3 text-[#0a66c2]">
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white text-sm">Kawasan Industri</div>
                                <div class="text-xs text-gray-500">Area pabrik, gudang</div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 3: Business Details & Environmental -->
        <div x-show="currentStep === 2" x-transition class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700">
            <div class="px-4 lg:px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Detail Bisnis & Lingkungan
                </h2>
            </div>

            <div class="px-4 lg:px-6 py-4 space-y-4">
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Jumlah Karyawan
                        </label>
                        <input 
                            type="number" 
                            name="number_of_employees" 
                            x-model="formData.number_of_employees"
                            min="0"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-transparent"
                            placeholder="Contoh: 25"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Kapasitas Produksi
                        </label>
                        <input 
                            type="text" 
                            name="production_capacity" 
                            x-model="formData.production_capacity"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-transparent"
                            placeholder="Contoh: 1000 unit/bulan"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Target Omzet Tahunan (Rp)
                        </label>
                        <input 
                            type="number" 
                            name="annual_revenue_target" 
                            x-model="formData.annual_revenue_target"
                            step="1000000"
                            min="0"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-transparent"
                            placeholder="Contoh: 10000000000"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Dampak Lingkungan <span class="text-red-500">*</span>
                        </label>
                        <select 
                            name="environmental_impact" 
                            x-model="formData.environmental_impact"
                            required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-transparent"
                        >
                            <option value="low">Rendah (Tidak ada limbah berbahaya)</option>
                            <option value="medium">Sedang (Limbah standar yang dikelola)</option>
                            <option value="high">Tinggi (Limbah B3 atau emisi signifikan)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Pengelolaan Limbah
                        </label>
                        <select 
                            name="waste_management" 
                            x-model="formData.waste_management"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-transparent"
                        >
                            <option value="">Pilih Tingkat Pengelolaan</option>
                            <option value="minimal">Minimal (Sampah umum)</option>
                            <option value="standard">Standard (Pemilahan limbah)</option>
                            <option value="complex">Kompleks (IPAL, TPS B3)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Status Kepemilikan
                        </label>
                        <select 
                            name="ownership_status" 
                            x-model="formData.ownership_status"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-transparent"
                        >
                            <option value="">Pilih Status</option>
                            <option value="owned">Milik Sendiri</option>
                            <option value="leased">Sewa</option>
                            <option value="partnership">Kerjasama</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="flex items-center">
                        <input 
                            type="checkbox" 
                            name="near_protected_area" 
                            x-model="formData.near_protected_area"
                            value="1"
                            class="mr-3 text-[#0a66c2] focus:ring-[#0a66c2]"
                        >
                        <span class="text-sm text-gray-700 dark:text-gray-300">
                            Lokasi dekat dengan kawasan lindung/konservasi
                        </span>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Catatan Tambahan
                    </label>
                    <textarea 
                        name="additional_notes" 
                        x-model="formData.additional_notes"
                        rows="3"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-transparent"
                        placeholder="Informasi tambahan yang relevan untuk perhitungan izin..."
                    ></textarea>
                </div>
            </div>
        </div>

        <!-- Step 4: Urgency & Confirmation -->
        <div x-show="currentStep === 3" x-transition class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700">
            <div class="px-4 lg:px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Tingkat Urgensi & Konfirmasi
                </h2>
            </div>

            <div class="px-4 lg:px-6 py-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        Tingkat Urgensi Pengurusan
                    </label>
                    <div class="grid md:grid-cols-2 gap-4">
                        <label class="flex items-center p-4 border-2 cursor-pointer transition-all active:scale-95"
                               :class="formData.urgency_level === 'standard' ? 'border-[#0a66c2] bg-[#0a66c2]/5' : 'border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700'">
                            <input type="radio" name="urgency_level" value="standard" x-model="formData.urgency_level" class="mr-3 text-[#0a66c2]">
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">Standard</div>
                                <div class="text-xs text-gray-500">Waktu normal sesuai prosedur</div>
                            </div>
                        </label>

                        <label class="flex items-center p-4 border-2 cursor-pointer transition-all active:scale-95"
                               :class="formData.urgency_level === 'rush' ? 'border-[#0a66c2] bg-[#0a66c2]/5' : 'border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700'">
                            <input type="radio" name="urgency_level" value="rush" x-model="formData.urgency_level" class="mr-3 text-[#0a66c2]">
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">Rush/Prioritas <span class="text-[#0a66c2]">(+50%)</span></div>
                                <div class="text-xs text-gray-500">Percepatan dengan biaya tambahan</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Summary -->
                <div class="bg-gray-50 dark:bg-gray-900 p-4">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Ringkasan Data</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                            <span class="text-gray-600 dark:text-gray-400">Skala Usaha:</span>
                            <span class="font-medium text-gray-900 dark:text-white capitalize" x-text="formData.business_scale || '-'"></span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                            <span class="text-gray-600 dark:text-gray-400">Luas Tanah:</span>
                            <span class="font-medium text-gray-900 dark:text-white" x-text="formData.land_area ? formData.land_area + ' m²' : '-'"></span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                            <span class="text-gray-600 dark:text-gray-400">Lokasi:</span>
                            <span class="font-medium text-gray-900 dark:text-white" x-text="formData.city && formData.province ? formData.city + ', ' + formData.province : '-'"></span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                            <span class="text-gray-600 dark:text-gray-400">Dampak Lingkungan:</span>
                            <span class="font-medium text-gray-900 dark:text-white capitalize" x-text="formData.environmental_impact || '-'"></span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="text-gray-600 dark:text-gray-400">Urgensi:</span>
                            <span class="font-medium text-gray-900 dark:text-white capitalize" x-text="formData.urgency_level || '-'"></span>
                        </div>
                    </div>
                </div>

                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 flex items-start">
                    <i class="fas fa-info-circle text-[#0a66c2] mt-0.5 mr-3"></i>
                    <div class="text-sm text-gray-700 dark:text-gray-300">
                        <strong>Catatan:</strong> Data yang Anda berikan akan digunakan untuk menghitung estimasi biaya perizinan yang lebih akurat, termasuk biaya pemerintah dan jasa konsultan BizMark. Estimasi final akan ditampilkan di halaman berikutnya.
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700">
            <div class="px-4 lg:px-6 py-4 flex items-center justify-between gap-4">
                <div>
                    <button 
                        type="button"
                        @click="prevStep"
                        x-show="currentStep > 0"
                        class="px-6 py-3 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold transition-all inline-flex items-center active:scale-95"
                    >
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </button>
                    
                    <a 
                        href="{{ route('client.services.show', $kbli->code) }}" 
                        x-show="currentStep === 0"
                        class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors"
                    >
                        Lewati (Rekomendasi Umum)
                    </a>
                </div>
                
                <div>
                    <button 
                        type="button"
                        @click="nextStep"
                        x-show="currentStep < steps.length - 1"
                        class="px-6 py-3 bg-[#0a66c2] hover:bg-[#004182] text-white font-semibold transition-all inline-flex items-center active:scale-95"
                    >
                        Lanjutkan
                        <i class="fas fa-arrow-right ml-2"></i>
                    </button>

                    <button 
                        type="submit"
                        x-show="currentStep === steps.length - 1"
                        :disabled="isSubmitting"
                        class="px-6 py-3 bg-[#0a66c2] hover:bg-[#004182] text-white font-semibold transition-all inline-flex items-center disabled:opacity-50 disabled:cursor-not-allowed active:scale-95"
                    >
                        <span x-show="!isSubmitting">
                            Dapatkan Rekomendasi
                            <i class="fas fa-arrow-right ml-2"></i>
                        </span>
                        <span x-show="isSubmitting" class="inline-flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Memproses...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Loading Overlay -->
    <div 
        x-show="isSubmitting"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        class="fixed inset-0 bg-gray-900/95 z-50 flex items-center justify-center"
        style="display: none;"
    >
        <div class="text-center max-w-md mx-auto px-6">
            <div class="relative inline-block mb-6">
                <div class="w-20 h-20 border-4 border-[#0a66c2]/20 border-t-[#0a66c2] rounded-full animate-spin"></div>
            </div>
            <h3 class="text-2xl font-semibold text-white mb-3">
                Menganalisis Data Anda
            </h3>
            <div class="mb-6">
                <p class="text-base text-gray-300" x-text="loadingMessages[loadingStep]"></p>
            </div>
            <div class="max-w-xs mx-auto">
                <div class="h-1.5 bg-gray-800 rounded-full overflow-hidden">
                    <div class="h-full bg-[#0a66c2] animate-progress-infinite"></div>
                </div>
                <p class="text-sm text-gray-400 mt-3">Mohon tunggu sebentar</p>
            </div>
        </div>
    </div>

    <style>
        @keyframes progress-infinite {
            0% { transform: translateX(-100%) scaleX(0.3); }
            50% { transform: translateX(50%) scaleX(0.6); }
            100% { transform: translateX(200%) scaleX(0.3); }
        }
        .animate-progress-infinite {
            animation: progress-infinite 2s ease-in-out infinite;
        }
    </style>
</div>

<script>
function contextForm() {
    return {
        currentStep: 0,
        isSubmitting: false,
        loadingStep: 0,
        steps: [
            'Skala Usaha',
            'Lokasi Proyek',
            'Detail Bisnis & Lingkungan',
            'Urgensi & Konfirmasi'
        ],
        loadingMessages: [
            'Menganalisis KBLI dan regulasi terkait',
            'Menghitung kompleksitas proyek',
            'Menyusun rekomendasi perizinan',
            'Menghitung estimasi biaya akurat',
            'Hampir selesai...'
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
            urgency_level: 'standard'
        },
        
        nextStep() {
            if (this.validateCurrentStep()) {
                if (this.currentStep < this.steps.length - 1) {
                    this.currentStep++;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
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
                if (!this.formData.business_scale) {
                    alert('Mohon pilih skala usaha');
                    return false;
                }
                if (!this.formData.land_area || this.formData.land_area <= 0) {
                    alert('Mohon isi luas tanah');
                    return false;
                }
            } else if (this.currentStep === 1) {
                if (!this.formData.province) {
                    alert('Mohon isi provinsi');
                    return false;
                }
                if (!this.formData.city) {
                    alert('Mohon isi kota/kabupaten');
                    return false;
                }
                if (!this.formData.location_category) {
                    alert('Mohon pilih kategori lokasi');
                    return false;
                }
            } else if (this.currentStep === 2) {
                if (!this.formData.environmental_impact) {
                    alert('Mohon pilih dampak lingkungan');
                    return false;
                }
            }
            return true;
        },
        
        submitForm(event) {
            if (!this.validateCurrentStep()) {
                return;
            }
            
            this.isSubmitting = true;
            
            // Cycle through loading messages
            const loadingInterval = setInterval(() => {
                if (this.loadingStep < this.loadingMessages.length - 1) {
                    this.loadingStep++;
                }
            }, 2000);
            
            // Submit form
            setTimeout(() => {
                event.target.submit();
            }, 500);
        },
        
        formatCurrency(value) {
            if (!value) return '';
            const billions = value / 1000000000;
            if (billions >= 1) {
                return 'Rp ' + billions.toFixed(2) + ' Miliar';
            }
            const millions = value / 1000000;
            return 'Rp ' + millions.toFixed(2) + ' Juta';
        }
    }
}
</script>
@endsection
