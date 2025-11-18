@extends('landing.layout')

@section('title', 'Kalkulator Perizinan - Estimasi Biaya & Waktu')
@section('description', 'Hitung estimasi biaya dan waktu pengurusan perizinan usaha Anda dengan Kalkulator Perizinan Bizmark.id. Gratis dan akurat.')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-blue-50 py-20">
    <div class="container mx-auto px-4 max-w-6xl">
        
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                Kalkulator <span class="text-purple-600">Perizinan Usaha</span>
            </h1>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Dapatkan estimasi biaya dan timeline pengurusan izin usaha Anda dalam hitungan detik
            </p>
        </div>

        <div class="grid lg:grid-cols-2 gap-8" x-data="calculatorApp()">
            
            <!-- Calculator Form -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">
                    <i class="fas fa-calculator text-purple-600 mr-2"></i>
                    Masukkan Data Usaha
                </h2>

                <form @submit.prevent="calculate()">
                    
                    <!-- Industry -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-industry mr-2"></i>Bidang Usaha
                        </label>
                        <select 
                            x-model="formData.industry" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        >
                            <option value="">Pilih Bidang Usaha</option>
                            @foreach($industries as $industry)
                                <option value="{{ $industry }}">{{ $industry }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Permit Type -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-certificate mr-2"></i>Jenis Izin
                        </label>
                        <select 
                            x-model="formData.permit_type_id" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        >
                            <option value="">Pilih Jenis Izin</option>
                            @foreach($permitTypes as $permit)
                                <option value="{{ $permit->id }}">{{ $permit->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- City -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-map-marker-alt mr-2"></i>Lokasi Usaha
                        </label>
                        <select 
                            x-model="formData.city" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        >
                            <option value="">Pilih Kota</option>
                            @foreach($cities as $city)
                                <option value="{{ $city }}">{{ $city }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Company Size -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-building mr-2"></i>Skala Usaha
                        </label>
                        <div class="grid grid-cols-3 gap-3">
                            <label class="relative">
                                <input 
                                    type="radio" 
                                    x-model="formData.company_size" 
                                    value="small" 
                                    required
                                    class="peer sr-only"
                                >
                                <div class="peer-checked:ring-2 peer-checked:ring-purple-600 peer-checked:bg-purple-50 border-2 border-gray-300 rounded-lg p-4 text-center cursor-pointer hover:border-purple-400 transition">
                                    <i class="fas fa-store text-2xl text-gray-600 mb-2"></i>
                                    <p class="font-semibold text-sm">Kecil</p>
                                    <p class="text-xs text-gray-500">1-20 orang</p>
                                </div>
                            </label>
                            <label class="relative">
                                <input 
                                    type="radio" 
                                    x-model="formData.company_size" 
                                    value="medium" 
                                    class="peer sr-only"
                                >
                                <div class="peer-checked:ring-2 peer-checked:ring-purple-600 peer-checked:bg-purple-50 border-2 border-gray-300 rounded-lg p-4 text-center cursor-pointer hover:border-purple-400 transition">
                                    <i class="fas fa-building text-2xl text-gray-600 mb-2"></i>
                                    <p class="font-semibold text-sm">Menengah</p>
                                    <p class="text-xs text-gray-500">21-100 orang</p>
                                </div>
                            </label>
                            <label class="relative">
                                <input 
                                    type="radio" 
                                    x-model="formData.company_size" 
                                    value="large" 
                                    class="peer sr-only"
                                >
                                <div class="peer-checked:ring-2 peer-checked:ring-purple-600 peer-checked:bg-purple-50 border-2 border-gray-300 rounded-lg p-4 text-center cursor-pointer hover:border-purple-400 transition">
                                    <i class="fas fa-city text-2xl text-gray-600 mb-2"></i>
                                    <p class="font-semibold text-sm">Besar</p>
                                    <p class="text-xs text-gray-500">>100 orang</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Urgency -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-tachometer-alt mr-2"></i>Kecepatan Proses
                        </label>
                        <div class="grid grid-cols-3 gap-3">
                            <label class="relative">
                                <input 
                                    type="radio" 
                                    x-model="formData.urgency" 
                                    value="normal" 
                                    required
                                    class="peer sr-only"
                                >
                                <div class="peer-checked:ring-2 peer-checked:ring-green-600 peer-checked:bg-green-50 border-2 border-gray-300 rounded-lg p-4 text-center cursor-pointer hover:border-green-400 transition">
                                    <i class="fas fa-clock text-2xl text-green-600 mb-2"></i>
                                    <p class="font-semibold text-sm">Normal</p>
                                    <p class="text-xs text-gray-500">Standar</p>
                                </div>
                            </label>
                            <label class="relative">
                                <input 
                                    type="radio" 
                                    x-model="formData.urgency" 
                                    value="fast" 
                                    class="peer sr-only"
                                >
                                <div class="peer-checked:ring-2 peer-checked:ring-orange-600 peer-checked:bg-orange-50 border-2 border-gray-300 rounded-lg p-4 text-center cursor-pointer hover:border-orange-400 transition">
                                    <i class="fas fa-bolt text-2xl text-orange-600 mb-2"></i>
                                    <p class="font-semibold text-sm">Cepat</p>
                                    <p class="text-xs text-gray-500">+50% biaya</p>
                                </div>
                            </label>
                            <label class="relative">
                                <input 
                                    type="radio" 
                                    x-model="formData.urgency" 
                                    value="express" 
                                    class="peer sr-only"
                                >
                                <div class="peer-checked:ring-2 peer-checked:ring-red-600 peer-checked:bg-red-50 border-2 border-gray-300 rounded-lg p-4 text-center cursor-pointer hover:border-red-400 transition">
                                    <i class="fas fa-rocket text-2xl text-red-600 mb-2"></i>
                                    <p class="font-semibold text-sm">Express</p>
                                    <p class="text-xs text-gray-500">+100% biaya</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit"
                        :disabled="loading"
                        class="w-full bg-gradient-to-r from-purple-600 to-purple-700 text-white font-semibold py-4 px-6 rounded-lg hover:from-purple-700 hover:to-purple-800 transition duration-300 shadow-lg disabled:opacity-50"
                    >
                        <span x-show="!loading">
                            <i class="fas fa-calculator mr-2"></i>Hitung Estimasi
                        </span>
                        <span x-show="loading">
                            <i class="fas fa-spinner fa-spin mr-2"></i>Menghitung...
                        </span>
                    </button>
                </form>
            </div>

            <!-- Results -->
            <div x-show="result" x-transition class="space-y-6">
                
                <!-- Cost & Timeline -->
                <div class="bg-gradient-to-br from-purple-600 to-purple-700 rounded-2xl shadow-xl p-8 text-white">
                    <h3 class="text-2xl font-bold mb-6">Estimasi Hasil</h3>
                    
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                            <p class="text-sm text-purple-100 mb-1">Estimasi Biaya</p>
                            <p class="text-2xl font-bold" x-text="'Rp ' + (result?.data?.estimated_cost || '0')"></p>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                            <p class="text-sm text-purple-100 mb-1">Estimasi Waktu</p>
                            <p class="text-2xl font-bold" x-text="(result?.data?.estimated_timeline || '0') + ' hari'"></p>
                        </div>
                    </div>

                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                        <p class="text-sm text-purple-100 mb-1">Tingkat Kompleksitas</p>
                        <p class="text-xl font-bold" x-text="result?.data?.complexity || '-'"></p>
                    </div>
                </div>

                <!-- Documents Checklist -->
                <div class="bg-white rounded-2xl shadow-xl p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">
                        <i class="fas fa-file-alt text-purple-600 mr-2"></i>
                        Dokumen yang Dibutuhkan
                    </h3>
                    <ul class="space-y-3">
                        <template x-for="doc in result?.data?.documents" :key="doc">
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span class="text-gray-700" x-text="doc"></span>
                            </li>
                        </template>
                    </ul>
                </div>

                <!-- CTA -->
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl shadow-xl p-8 text-white text-center">
                    <h3 class="text-2xl font-bold mb-3">Mulai Konsultasi Gratis</h3>
                    <p class="mb-6 text-blue-100">Diskusikan kebutuhan perizinan Anda dengan expert kami</p>
                    <a 
                        href="https://wa.me/62838796028550?text=Halo,%20saya%20tertarik%20untuk%20konsultasi%20perizinan" 
                        target="_blank"
                        class="inline-block bg-white text-blue-600 font-semibold py-3 px-8 rounded-lg hover:bg-blue-50 transition"
                    >
                        <i class="fab fa-whatsapp mr-2"></i>WhatsApp Kami
                    </a>
                </div>

            </div>

        </div>

    </div>
</div>

<script>
function calculatorApp() {
    return {
        formData: {
            industry: '',
            permit_type_id: '',
            city: '',
            company_size: '',
            urgency: ''
        },
        result: null,
        loading: false,

        async calculate() {
            this.loading = true;
            
            try {
                const response = await fetch('{{ route("calculator.calculate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.formData)
                });

                const data = await response.json();
                
                if (data.success) {
                    this.result = data;
                    
                    // Scroll to results
                    setTimeout(() => {
                        window.scrollTo({
                            top: document.querySelector('[x-show="result"]').offsetTop - 100,
                            behavior: 'smooth'
                        });
                    }, 100);
                    
                    // Track conversion
                    if (typeof gtag !== 'undefined') {
                        gtag('event', 'calculator_complete', {
                            'event_category': 'Tools',
                            'event_label': 'Permit Calculator'
                        });
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endsection
